<?php
namespace DejwCake\AdminBakeTheme\Test\TestCase\Shell\Task;

use Cake\TestSuite\TestCase;
use DejwCake\AdminBakeTheme\Shell\Task\AdminControllerTask;

/**
 * DejwCake\AdminBakeTheme\Shell\Task\AdminControllerTask Test Case
 */
class AdminControllerTaskTest extends TestCase
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
     * @var \DejwCake\AdminBakeTheme\Shell\Task\AdminControllerTask
     */
    public $AdminController;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->AdminController = $this->getMockBuilder('DejwCake\AdminBakeTheme\Shell\Task\AdminControllerTask')
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
        unset($this->AdminController);

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
