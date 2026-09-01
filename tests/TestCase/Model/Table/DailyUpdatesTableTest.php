<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DailyUpdatesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DailyUpdatesTable Test Case
 */
class DailyUpdatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DailyUpdatesTable
     */
    protected $DailyUpdates;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.DailyUpdates',
        'app.Projects',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('DailyUpdates') ? [] : ['className' => DailyUpdatesTable::class];
        $this->DailyUpdates = $this->getTableLocator()->get('DailyUpdates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->DailyUpdates);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\DailyUpdatesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\DailyUpdatesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
