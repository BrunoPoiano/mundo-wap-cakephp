<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateWorkdays extends AbstractMigration
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
        $table = $this->table("workdays");
        $table
            ->addColumn("date", "date", [
                "null" => false,
            ])
            ->addColumn("visits", "integer", [
                "null" => false,
                "default" => "0",
            ])
            ->addColumn("completed", "integer", [
                "null" => false,
                "default" => "0",
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
