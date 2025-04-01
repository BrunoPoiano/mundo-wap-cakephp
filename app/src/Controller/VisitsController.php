<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\VisitsTable;
use Cake\Controller\Controller;
use Cake\Http\Client;
use Cake\Validation\Validator;
use Cake\Http\Response;
use Cake\Log\Log;
use DateTime;

class VisitsController extends Controller
{
    // Initialize models and components
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel("Visits");
        $this->loadModel("Addresses");
        $this->loadModel("Workdays");

        $this->loadComponent("Address");
        $this->loadComponent("Visit");
        $this->loadComponent("Workday");
    }

    // List visits with pagination
    public function list()
    {
        $currentPage = (int) $this->request->getQuery("current_page", 1);
        $perPage = (int) $this->request->getQuery("per_page", 5);
        $order = $this->request->getQuery("order", "DESC");
        $date = $this->request->getQuery("date");

        $query = $this->Visits->find();
        $total = $query->count();

        // Filter by date if provided
        if ($date !== null) {
            $query->where(["date" => $date]);
        }

        $this->paginate = [
            "limit" => $perPage,
            "page" => $currentPage,
            "order" => ["Visits.created" => $order],
        ];

        $visits = $this->paginate($query);

        $return = [
            "data" => $visits,
            "current_page" => $currentPage,
            "per_page" => $perPage,
            "last_page" => ceil($total / $perPage),
            "total" => $total,
        ];

        return $this->response
            ->withStringBody(json_encode($return))
            ->withType("application/json")
            ->withStatus(200);
    }

    // Get visit by date
    public function date()
    {
        $date = $this->request->getQuery(
            "date",
            DateTime::createFromFormat("Y-m-d", "2023-01-01")
        );

        $visits = $this->Visits
            ->find()
            ->where(["date" => $date])
            ->first();

        return $this->response
            ->withStringBody(json_encode($visits))
            ->withType("application/json")
            ->withStatus(200);
    }

    // Create new visit
    public function create()
    {
        $requestData = $this->request->getData();

        // Validate input
        $visitsValidation = $this->Visit->validateVisit($requestData);
        if ($visitsValidation["error"] !== 0) {
            return $this->response
                ->withStatus($visitsValidation["error"])
                ->withStringBody(json_encode($visitsValidation["message"]))
                ->withType("application/json");
        }

        // Calculate duration
        $duration = $requestData["forms"] * 5 + $requestData["products"] * 15;

        // Check workday availability
        $workdayCheck = $this->Workday->checkWorkday(
            $requestData["date"],
            $duration
        );
        if ($workdayCheck["error"] !== 0) {
            return $this->response
                ->withStatus($workdayCheck["error"])
                ->withStringBody(json_encode($workdayCheck["message"]))
                ->withType("application/json");
        }

        // Get postal code data
        $postalCodeData = $this->Addresses->getPostalCodeData(
            $requestData["address_postal_code"]
        );

        if ($postalCodeData == null) {
            return $this->response
                ->withStatus(404)
                ->withStringBody(json_encode("Postal Code not found"))
                ->withType("application/json");
        }

        try {
            // Create visit
            $visits = $this->Visits->newEntity([
                ...$requestData,
                "duration" => $duration,
            ]);

            $this->Visits->save($visits);
            // Create address
            $addressData = [
                ...$postalCodeData,
                "foreign_table" => "visits",
                "foreign_id" => $visits["id"],
                "street_number" => $requestData["address_street_number"],
                "complement" => $requestData["address_complement"],
            ];

            $addressResponse = $this->Address->createAddress($addressData);
            if ($addressResponse["error"] !== 0) {
                return $this->response
                    ->withStatus($addressResponse["error"])
                    ->withStringBody(json_encode($addressResponse["message"]))
                    ->withType("application/json");
            }

            return $this->response
                ->withStringBody(
                    json_encode([
                        "message" => "Visit saved successfully",
                        "data" => $visits,
                    ])
                )
                ->withType("application/json")
                ->withStatus(201);
        } catch (\Exception $e) {
            return $this->response
                ->withStringBody(
                    json_encode([
                        "message" => "Failed to save visits",
                        "error" => $e->getMessage(),
                    ])
                )
                ->withType("application/json")
                ->withStatus(500);
        }
    }

    // Edit existing visit
    public function edit()
    {
        $requestData = $this->request->getData();

        // Validate input
        $visitsValidation = $this->Visit->validateVisit($requestData);
        if ($visitsValidation["error"] !== 0) {
            return $this->response
                ->withStatus($visitsValidation["error"])
                ->withStringBody(json_encode($visitsValidation["message"]))
                ->withType("application/json");
        }
        $id = $this->request->getParam("id");

        // Calculate duration
        $duration = $requestData["forms"] * 5 + $requestData["products"] * 15;

        // Check workday availability
        $workdayCheck = $this->Workday->checkWorkdayEdit(
            $requestData["date"],
            $duration,
            $id
        );

        if ($workdayCheck["error"] !== 0) {
            return $this->response
                ->withStatus($workdayCheck["error"])
                ->withStringBody(json_encode($workdayCheck["message"]))
                ->withType("application/json");
        }

        try {
            // Get visit and address
            $visits = $this->Visits->get($id);
            $address = $this->Addresses
                ->find()
                ->where(["foreign_table" => "visits", "foreign_id" => $id])
                ->first();

            // Delete old address if postal code changed
            if (
                $address &&
                $address->postal_code !== $requestData["address_postal_code"]
            ) {
                $this->Addresses->delete($address);

                // Get postal code data
                $postalCodeData = $this->Addresses->getPostalCodeData(
                    $requestData["address_postal_code"]
                );

                if ($postalCodeData === null) {
                    return $this->response
                        ->withStatus(404)
                        ->withStringBody(json_encode("Postal Code not found"))
                        ->withType("application/json");
                }
                // Create new address
                $addressData = [
                    ...$postalCodeData,
                    "foreign_table" => "visits",
                    "foreign_id" => $visits->id,
                    "street_number" => $requestData["address_street_number"],
                    "complement" => $requestData["address_complement"],
                ];
                $addressResponse = $this->Address->createAddress($addressData);
                if ($addressResponse["error"] !== 0) {
                    return $this->response
                        ->withStatus($addressResponse["error"])
                        ->withStringBody(
                            json_encode($addressResponse["message"])
                        )
                        ->withType("application/json");
                }
            }

            $oldDate = json_decode(json_encode($visits->date));
            $newDate = $requestData["date"];

            // Update visit
            $this->Visits->patchEntity($visits, [
                ...$requestData,
                "duration" => $duration,
            ]);

            $this->Visits->save($visits);

            $this->Workday->refreshWorkday($newDate);
            $this->Workday->refreshWorkday($oldDate);

            return $this->response
                ->withType("application/json")
                ->withStringBody(json_encode($visits))
                ->withStatus(200);
        } catch (\Exception $e) {
            return $this->response
                ->withStringBody(
                    json_encode([
                        "error" => "Failed to update visit",
                        "message" => $e->getMessage(),
                    ])
                )
                ->withType("application/json")
                ->withStatus(500);
        }
    }

    // Delete visit
    public function delete()
    {
        try {
            $id = $this->request->getParam("id");
            $visit = $this->Visits->get($id);

            $this->Visits->delete($visit);
            return $this->response
                ->withStringBody(json_encode("Visit deleted successfully"))
                ->withType("application/json")
                ->withStatus(200);
        } catch (\Exception $e) {
            return $this->response
                ->withStringBody(
                    json_encode([
                        "message" => "Failed to delete visit",
                        "error" => $e->getMessage(),
                    ])
                )
                ->withType("application/json")
                ->withStatus(500);
        }
    }

    // Patch visit attributes
    public function patch()
    {
        try {
            $requestData = $this->request->getData();

            if (!isset($requestData["completed"])) {
                return $this->response
                    ->withStringBody(
                        json_encode(
                            "Failed to patch visit: Missing required field 'completed'"
                        )
                    )
                    ->withType("application/json")
                    ->withStatus(400);
            }

            $id = $this->request->getParam("id");

            $visit = $this->Visits->get($id);

            $this->Visits->patchEntity($visit, [
                "completed" => $requestData["completed"],
            ]);
            $this->Visits->save($visit);

            return $this->response
                ->withStringBody(json_encode("Visit patched successfully"))
                ->withType("application/json")
                ->withStatus(200);
        } catch (\Throwable $e) {
            // Catch all errors, including warnings
            return $this->response
                ->withStringBody(
                    json_encode([
                        "message" => "Failed to patch visit",
                        "error" => $e->getMessage(),
                    ])
                )
                ->withType("application/json")
                ->withStatus(500);
        }
    }
}
