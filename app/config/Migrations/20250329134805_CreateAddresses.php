<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateAddresses extends AbstractMigration
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
        $table = $this->table("addresses");
        $table
            ->addColumn("foreign_table", "string", [
                "limit" => 100,
                "null" => false,
            ])
            ->addColumn("foreign_id", "integer", [
                "null" => false,
            ])
            ->addColumn("postal_code", "string", [
                "limit" => 8,
                "null" => false,
            ])
            ->addColumn("state", "string", [
                "limit" => 2,
                "null" => false,
            ])
            ->addColumn("city", "string", [
                "limit" => 200,
                "null" => false,
            ])
            ->addColumn("sublocality", "string", [
                "limit" => 200,
                "null" => false,
            ])
            ->addColumn("street", "string", [
                "limit" => 200,
                "null" => false,
            ])
            ->addColumn("street_number", "string", [
                "limit" => 200,
                "null" => false,
            ])
            ->addColumn("complement", "string", [
                "limit" => 200,
                "null" => false,
                "default" => "",
            ])
            ->addPrimaryKey("id")
            ->addIndex(
                ["foreign_table", "foreign_id"],
                ["name" => "foreign_table__foreign_id"]
            )
            ->create();
    }
}
