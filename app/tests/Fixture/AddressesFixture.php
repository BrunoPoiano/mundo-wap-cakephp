<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AddressesFixture
 */
class AddressesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'foreign_table' => 'Lorem ipsum dolor sit amet',
                'foreign_id' => 1,
                'postal_code' => 'Lorem ',
                'state' => 'Lo',
                'city' => 'Lorem ipsum dolor sit amet',
                'sublocality' => 'Lorem ipsum dolor sit amet',
                'street' => 'Lorem ipsum dolor sit amet',
                'street_number' => 'Lorem ipsum dolor sit amet',
                'complement' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
