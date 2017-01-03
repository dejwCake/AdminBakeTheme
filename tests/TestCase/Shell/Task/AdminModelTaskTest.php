<?php
namespace DejwCake\AdminBakeTheme\Test\TestCase\Shell\Task;

use Cake\TestSuite\TestCase;
use DejwCake\AdminBakeTheme\Shell\Task\AdminModelTask;

/**
 * DejwCake\AdminBakeTheme\Shell\Task\AdminModelTask Test Case
 */
class AdminModelTaskTest extends TestCase
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
     * @var \DejwCake\AdminBakeTheme\Shell\Task\AdminModelTask
     */
    public $AdminModel;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->AdminModel = $this->getMockBuilder('DejwCake\AdminBakeTheme\Shell\Task\AdminModelTask')
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
        unset($this->AdminModel);

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
