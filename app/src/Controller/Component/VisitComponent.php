<?php
declare(strict_types=1);
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

class VisitComponent extends Component
{
    public function validateVisit($requestData)
    {
        // Create a new validator instance
        $validatorObj = new Validator();
        $validatorObj
            ->requirePresence("date", "create")
            ->notEmptyDate("date", "date cannot be empty")
            ->date("date", ["ymd"], "Invalid date format (YYYY-MM-DD expected)")
            ->add("date", "customValidation", [
                "rule" => function ($value, $context) {
                    $today = new FrozenDate(date("Y-m-d"));
                    $value = new FrozenDate($value);

                    return $value->greaterThanOrEquals($today);
                },
                "message" => "Date must be today or later " . date("Y-m-d"),
            ])

            ->allowEmptyString("completed", "create")
            ->boolean("completed")
            ->requirePresence("forms", "create")
            ->greaterThanOrEqual("forms", 0, "Forms cannot be less than 0")
            ->requirePresence("products", "create")
            ->greaterThanOrEqual(
                "products",
                0,
                "Products cannot be less than 0"
            )
            ->requirePresence("address_postal_code", "create")
            ->notEmptyString(
                "address_postal_code",
                "postal_code cannot be empty"
            )
            ->requirePresence("address_street_number", "create")
            ->notEmptyString(
                "address_street_number",
                "street_number cannot be empty"
            );

        // Validate the request data against our rules
        $errorList = $validatorObj->validate($requestData);

        // Return error response if validation failed
        if (!empty($errorList)) {
            return [
                "error" => 400,
                "message" => $errorList,
                "data" => "",
            ];
        }

        // Return success response
        return [
            "error" => 0,
            "message" => "",
            "data" => "",
        ];
    }
}
