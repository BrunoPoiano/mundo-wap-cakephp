<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateVisits extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table("visits");
        $table
            ->addColumn("date", "date", [
                "null" => false,
            ])
            ->addColumn("completed", "integer", [
                "null" => false,
                "default" => "0",
            ])
            ->addColumn("forms", "integer", [
                "null" => false,
            ])
            ->addColumn("products", "integer", [
                "null" => false,
            ])
            ->addColumn("duration", "integer", [
                "null" => false,
                "default" => "0",
            ])
            ->addPrimaryKey("id")
            ->addIndex(["date"], ["name" => "date"])
            ->create();
    }
}
