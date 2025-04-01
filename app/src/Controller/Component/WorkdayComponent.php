<?php
declare(strict_types=1);
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

class WorkdayComponent extends Component
{
    public function checkWorkday($date, $duration)
    {
        // Get workday table
        $workdayTable = TableRegistry::getTableLocator()->get("Workdays");
        $workday = $workdayTable
            ->find()
            ->where(["date" => $date])
            ->first();

        // Create workday if not exists
        if ($workday === null) {
            $workday = $this->createWorkday([
                "date" => $date,
                "visits" => 0,
                "completed" => 0,
                "duration" => 0,
            ])["data"];
        }

        // Check if workday is completed
        if (intval($workday->completed) === 1) {
            return [
                "error" => 500,
                "message" => "Workday already completed",
                "data" => null,
            ];
        }

        // Check if duration exceeds limit
        if ($workday->duration > 480 || $workday->duration + $duration > 480) {
            return [
                "error" => 500,
                "message" =>
                    "Workday duration exceeds maximum allowed duration ",
                "data" => null,
            ];
        }

        // Update workday
        $workday = $this->editWorkday($workday, [
            "date" => $date,
            "visits" => $workday->visits + 1,
            "completed" => 0,
            "duration" => $workday->duration + $duration,
        ])["data"];

        return [
            "error" => 0,
            "message" => "",
            "data" => null,
        ];
    }

    public function checkWorkdayEdit($date, $newDuration, $visitId)
    {
        // Get required tables
        $workdayTable = TableRegistry::getTableLocator()->get("Workdays");
        $visitsTable = TableRegistry::getTableLocator()->get("Visits");

        $sql =
            "SELECT SUM(duration) AS total_duration FROM visits WHERE date = :date AND id != :visit_id";

        // Get total duration for the day excluding current visit
        $visitDay = $visitsTable
            ->getConnection()
            ->execute($sql, ["date" => $date, "visit_id" => $visitId])
            ->fetch("assoc");

        // Check if duration exceeds limit
        if (
            $visitDay["total_duration"] > 480 ||
            $visitDay["total_duration"] + $newDuration > 480
        ) {
            return [
                "error" => 500,
                "message" => "Edit exceeds maximum allowed duration ",
                "data" => null,
            ];
        }

        // Get workday
        $workday = $workdayTable
            ->find()
            ->where(["date" => $date])
            ->first();

        // Create workday if not exists
        if ($workday === null) {
            $workday = $this->createWorkday([
                "date" => $date,
                "visits" => 0,
                "completed" => 0,
                "duration" => 0,
            ])["data"];
        }

        // Check if workday is completed
        if (intval($workday->completed) === 1) {
            return [
                "error" => 500,
                "message" => "Workday already completed",
                "data" => null,
            ];
        }

        // Update workday
        $workday = $this->editWorkday($workday, [
            "visits" => $workday->visits + 1,
            "duration" => $workday->duration + $newDuration,
        ])["data"];

        return [
            "error" => 0,
            "message" => "",
            "data" => null,
        ];
    }

    public function refreshWorkday($date): void
    {
        // Get required tables
        $workdayTable = TableRegistry::getTableLocator()->get("Workdays");
        $visitsTable = TableRegistry::getTableLocator()->get("Visits");

        $visits = $visitsTable
            ->find()
            ->where(["date" => $date])
            ->all();

        $visitDay = $visitsTable
            ->getConnection()
            ->execute(
                "SELECT SUM(duration) AS total_duration FROM visits WHERE date = :date",
                ["date" => $date]
            )
            ->fetch("assoc");

        $totalDuration = $visitDay["total_duration"] ?? 0;

        Log::info(json_encode($visitDay));
        Log::info(
            json_encode(["date" => $date, "duratrion" => $totalDuration])
        );

        // Get workday
        $workday = $workdayTable
            ->find()
            ->where(["date" => $date])
            ->first();

        // Create workday if not exists
        if ($workday === null) {
            $workday = $this->createWorkday([
                "date" => $date,
                "visits" => 0,
                "completed" => 0,
                "duration" => 0,
            ])["data"];
        }

        // Update workday
        $this->editWorkday($workday, [
            "visits" => $visits->count(),
            "duration" => $totalDuration,
        ]);
    }

    public function createWorkday($workdayData)
    {
        try {
            // Get workday table
            $workdayTable = TableRegistry::getTableLocator()->get("Workdays");

            $workday = $workdayTable->newEntity($workdayData);
            $workdayTable->save($workday);

            return [
                "error" => 0,
                "message" => "Workday saved successfully",
                "data" => $workday,
            ];
        } catch (\Exception $e) {
            return [
                "error" => 500,
                "message" => "Failed to save workday: " . $e->getMessage(),
            ];
        }
    }

    public function editWorkday($workday, $requestData)
    {
        try {
            // Get workday table
            $workdayTable = TableRegistry::getTableLocator()->get("Workdays");
            $workday = $workdayTable->patchEntity($workday, $requestData);
            $workdayTable->save($workday);

            return [
                "error" => 0,
                "message" => "Workday Edited successfully",
                "data" => $workday,
            ];
        } catch (\Exception $e) {
            return [
                "error" => 500,
                "message" => "Failed to edit workday: " . $e->getMessage(),
            ];
        }
    }

    public function pathWorkday($date, $path): void
    {
        // Get workday table
        $workdayTable = TableRegistry::getTableLocator()->get("Workdays");

        $workday = $workdayTable
            ->find()
            ->where(["date" => $date])
            ->first();

        // Create workday if not exists
        if ($workday === null) {
            $workday = $this->createWorkday([
                "date" => $date,
                "visits" => 0,
                "completed" => 0,
                "duration" => 0,
            ])["data"];
        }

        $workdayTable->patchEntity($workday, $path);
        $workdayTable->save($workday);
    }
}
