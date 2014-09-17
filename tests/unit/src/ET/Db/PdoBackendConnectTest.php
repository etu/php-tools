<?php
namespace Tests\ET\Db;

use \ET\Db\PdoBackend;
use \ET\Config;

use \Phockito as P;
use \Hamcrest_Matchers as H;

class PdoBackendConnectTest extends \PHPUnit_Framework_TestCase
{
    /** @var PdoBackend */
    private $target;

    /** @var Config */
    private $configMock;

    public function setUp()
    {
        $this->configMock = P::mock(Config::class);

        $this->target = new PdoBackend();
    }

    /**
     * @test
     * @expectedException ET\Db\DbException
     */
    public function shouldFailOnMissingDSN()
    {
        // Test
        $this->target->connect($this->configMock);
    }

    /**
     * @test
     * @expectedException ET\Db\DbException
     */
    public function shouldFailOnMissingUsernameOrPassword()
    {
        // Fixture
        P::when($this->configMock)->__get('db')->return(
            (object) [
                'dsn' => 'sqlite::memory:'
            ]
        );

        // Test
        $this->target->connect($this->configMock);
    }

    /**
     * @test
     * @expectedException ET\Db\DbException
     */
    public function shouldFailOnInvalidDSN()
    {
        // Fixture
        P::when($this->configMock)->__get('db')->return(
            (object) [
                'dsn' => 'invalid::driver:',
                'username' => '',
                'password' => ''
            ]
        );

        // Test
        $this->target->connect($this->configMock);
    }

    /**
     * @test
     */
    public function shouldConnect()
    {
        // Fixture
        P::when($this->configMock)->__get('db')->return(
            (object) [
                'dsn' => 'sqlite::memory:',
                'username' => '',
                'password' => ''
            ]
        );

        // Test
        $actual = $this->target->connect($this->configMock);

        // Assert
        $this->assertTrue($actual);
    }
}
