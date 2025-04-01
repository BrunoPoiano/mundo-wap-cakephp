<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WorkdaysTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WorkdaysTable Test Case
 */
class WorkdaysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\WorkdaysTable
     */
    protected $Workdays;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Workdays',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Workdays') ? [] : ['className' => WorkdaysTable::class];
        $this->Workdays = $this->getTableLocator()->get('Workdays', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Workdays);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\WorkdaysTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
