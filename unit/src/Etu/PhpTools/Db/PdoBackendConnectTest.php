<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
namespace Tests\Etu\PhpTools\Db;

use \Etu\PhpTools\Db\PdoBackend;
use \Etu\PhpTools\Config;

class PdoBackendConnectTest extends \PHPUnit_Framework_TestCase
{
    /** @var PdoBackend */
    private $target;

    /** @var Config */
    private $configMock;

    public function setUp()
    {
        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()->getMock();

        $this->target = new PdoBackend();
    }

    /**
     * @test
     * @expectedException Etu\PhpTools\Db\DbException
     */
    public function shouldFailOnMissingDSN()
    {
        // Test
        $this->target->connect($this->configMock);
    }

    /**
     * @test
     * @expectedException Etu\PhpTools\Db\DbException
     */
    public function shouldFailOnMissingUsernameOrPassword()
    {
        // Fixture
        $this->configMock->method('__get')->with('db')->will($this->returnValue(
            (object) [
                'dsn' => 'sqlite::memory:'
            ]
        ));

        // Test
        $this->target->connect($this->configMock);
    }

    /**
     * @test
     * @expectedException Etu\PhpTools\Db\DbException
     */
    public function shouldFailOnInvalidDSN()
    {
        // Fixture
        $this->configMock->method('__get')->with('db')->will($this->returnValue(
            (object) [
                'dsn' => 'invalid::driver:',
                'username' => '',
                'password' => ''
            ]
        ));

        // Test
        $this->target->connect($this->configMock);
    }

    /**
     * @test
     */
    public function shouldConnect()
    {
        // Fixture
        $this->configMock->method('__get')->with('db')->will($this->returnValue(
            (object) [
                'dsn' => 'sqlite::memory:',
                'username' => '',
                'password' => ''
            ]
        ));

        // Test
        $actual = $this->target->connect($this->configMock);

        // Assert
        $this->assertTrue($actual);
    }
}
