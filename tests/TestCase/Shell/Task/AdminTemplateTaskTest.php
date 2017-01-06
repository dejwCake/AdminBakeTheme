<?php
namespace DejwCake\AdminBakeTheme\Test\TestCase\Shell\Task;

use Cake\TestSuite\TestCase;
use DejwCake\AdminBakeTheme\Shell\Task\AdminTemplateTask;

/**
 * DejwCake\AdminBakeTheme\Shell\Task\AdminTemplateTask Test Case
 */
class AdminTemplateTaskTest extends TestCase
{

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \DejwCake\AdminBakeTheme\Shell\Task\AdminTemplateTask
     */
    public $AdminTemplate;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->AdminTemplate = $this->getMockBuilder('DejwCake\AdminBakeTheme\Shell\Task\AdminTemplateTask')
            ->setConstructorArgs([$this->io])
            ->getMock();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AdminTemplate);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
