<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\I18n\FrozenDate;

class WorkdaysController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel("Workdays");
        $this->loadModel("Visits");

        $this->loadComponent("Workday");
    }

    public function list()
    {
        // Get pagination parameters from request
        $currentPage = (int) $this->request->getQuery("current_page", 1);
        $perPage = (int) $this->request->getQuery("per_page", 5);
        $order = $this->request->getQuery("order", "DESC");
        $date = $this->request->getQuery("date");

        // Initialize query and count total records
        $query = $this->Workdays->find();
        $total = $query->count();

        // Filter by date if provided
        if ($date !== null) {
            $query->where(["date" => $date]);
        }

        // Set pagination options
        $this->paginate = [
            "limit" => $perPage,
            "page" => $currentPage,
            "order" => ["Visits.created" => $order],
        ];

        // Paginate the query
        $workdays = $this->paginate($query);

        // Prepare response data
        $return = [
            "data" => $workdays,
            "current_page" => $currentPage,
            "per_page" => $perPage,
            "last_page" => ceil($total / $perPage),
            "total" => $total,
        ];

        // Return JSON response
        return $this->response
            ->withStringBody(json_encode($return))
            ->withType("application/json")
            ->withStatus(200);
    }

    public function closeWorkDay()
    {
        // Get request data
        $requestData = $this->request->getData();

        // Find incomplete visits for the specified date
        $visits = $this->Visits
            ->find()
            ->where(["date" => $requestData["date"], "completed" => 0])
            ->all();

        // Calculate next day date
        $newDate = (new FrozenDate($requestData["date"]))
            ->addDays(1)
            ->format("Y-m-d");

        // Reschedule all incomplete visits to next day
        foreach ($visits as $visit) {
            $this->saveItemsOnNextDay($newDate, $visit);
        }

        // Mark workday as completed
        $this->Workday->pathWorkday($requestData["date"], ["completed" => 1]);
        $this->Workday->refreshWorkday($requestData["date"]);
        // Return success response
        return $this->response
            ->withStringBody("atualizado com sucesso")
            ->withType("application/json")
            ->withStatus(200);
    }

    public function saveItemsOnNextDay(&$date, $visit, $depth = 0): void
    {
        // Prevent infinite recursion
        if ($depth > 30) {
            throw new \RuntimeException(
                "Exceeded max recursion depth while trying to reschedule visits."
            );
        }

        // SQL to get total duration of visits for the date
        $sql =
            "SELECT SUM(duration) AS total_duration FROM visits WHERE date = :date";

        // Get current total minutes for the day
        $currentTotalMinutes = $this->Visits
            ->getConnection()
            ->execute($sql, ["date" => $date])
            ->fetch("assoc");

        $currentTotalMinutes =
            intval($currentTotalMinutes["total_duration"]) ?? 0;

        // If day is already full, move to next day
        if ($currentTotalMinutes >= 480) {
            $date = (new FrozenDate($date))->addDays(1)->format("Y-m-d");

            $this->saveItemsOnNextDay($date, $visit, $depth + 1);
            return;
        }

        // Calculate new total with current visit
        $newTotalMinutes =
            intval($currentTotalMinutes) + intval($visit->duration);

        // If adding this visit exceeds workday limit, move to next day
        if ($newTotalMinutes >= 480) {
            $date = (new FrozenDate($date))->addDays(1)->format("Y-m-d");

            $this->saveItemsOnNextDay($date, $visit, $depth + 1);
            return;
        }

        // Save visit to the current date
        $this->Visits->patchEntity($visit, [
            "date" => $date,
        ]);
        $this->Visits->save($visit);

        // Refresh workday data
        $this->Workday->refreshWorkday($date);
    }
}
