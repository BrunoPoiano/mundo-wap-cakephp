<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\VisitsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\VisitsTable Test Case
 */
class VisitsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\VisitsTable
     */
    protected $Visits;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Visits',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Visits') ? [] : ['className' => VisitsTable::class];
        $this->Visits = $this->getTableLocator()->get('Visits', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Visits);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\VisitsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
