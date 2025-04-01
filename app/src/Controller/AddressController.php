<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;

class AddressController extends Controller
{
    // Initialize controller
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel("Addresses");
        $this->loadComponent("Address");
    }

    // List addresses with pagination
    public function list()
    {
        $currentPage = $this->request->getQuery("current_page", 1);
        $perPage = $this->request->getQuery("per_page", 5);
        $order = $this->request->getQuery("order", "DESC");

        $this->paginate = [
            "limit" => $perPage,
            "page" => $currentPage,
            "order" => ["Addresses.created" => $order],
        ];

        $address = $this->paginate($this->Addresses);

        $return = [
            "data" => $address,
            "current_page" => $currentPage,
            "per_page" => $perPage,
            "last_page" => ceil($this->Addresses->find()->count() / $perPage),
            "total" => $this->Addresses->find()->count(),
        ];

        return $this->response
            ->withStatus(200)
            ->withStringBody(json_encode($return))
            ->withType("application/json");
    }

    // Delete address
    public function delete()
    {
        try {
            $id = $this->request->getParam("id");
            $address = $this->Addresses->get($id);

            $this->Addresses->delete($address);
            return $this->response
                ->withStatus(204)
                ->withType("application/json");
        } catch (\Exception $e) {
            return $this->response
                ->withStatus(500)
                ->withStringBody(
                    json_encode([
                        "message" => "Failed to delete address",
                        "error" => $e->getMessage(),
                    ])
                )
                ->withType("application/json");
        }
    }
}
