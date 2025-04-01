<?php
declare(strict_types=1);
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

class AddressComponent extends Component
{
    public function validateAddress($requestData)
    {
        // Create new validator instance
        $validatorObj = new Validator();
        $validatorObj
            ->requirePresence("postal_code", "create")
            ->notEmptyString("postal_code", "postal_code cannot be empty")
            ->minLength(
                "postal_code",
                8,
                "postal_code must be at least 8 characters long"
            )
            ->requirePresence("street_number", "create")
            ->notEmptyString("street_number", "street_number cannot be empty");

        // Validate request data
        $errorsData = $validatorObj->validate($requestData);

        if (!empty($errorsData)) {
            return [
                "error" => 400,
                "message" => $errorsData,
                "data" => "",
            ];
        }

        return [
            "error" => 0,
            "message" => "",
            "data" => "",
        ];
    }

    public function createAddress($requestData)
    {
        // Validate address data
        $validationAddressData = $this->validateAddress($requestData);

        if ($validationAddressData["error"] !== 0) {
            return [
                "error" => $validationAddressData["error"],
                "message" => $validationAddressData["error"],
            ];
        }

        // Get addresses table
        $addressesTableObj = TableRegistry::getTableLocator()->get("Addresses");

        try {
            // Create and save new address
            $addressObj = $addressesTableObj->newEntity($requestData);
            $addressesTableObj->save($addressObj);

            return [
                "error" => 0,
                "message" => "Address saved successfully",
                "data" => $addressObj,
            ];
        } catch (\Exception $exceptionObj) {
            return [
                "error" => 500,
                "message" =>
                    "Failed to save address: " . $exceptionObj->getMessage(),
            ];
        }
    }

    public function deleteAddress($idValue)
    {
        try {
            // Get address by id
            $addressObj = $this->Addresses->get($idValue);

            // Delete the address
            $this->Addresses->delete($addressObj);
            return [
                "error" => 0,
                "message" => "Address deleted successfully",
                "data" => "",
            ];
        } catch (\Exception $exceptionObj) {
            return [
                "error" => 500,
                "message" =>
                    "Failed to delete address: " .
                    json_encode($exceptionObj->getMessage()),
            ];
        }
    }
}
