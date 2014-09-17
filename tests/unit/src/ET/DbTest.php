<?php
namespace Tests\ET;

use \ET\Db;
use \ET\Config;
use \ET\Db\PdoBackend;

use \Phockito as P;
use \Hamcrest_Matchers as H;

class DbTest extends \PHPUnit_Framework_TestCase
{
    /** @var Db */
    private $target;

    /** @var Config */
    private $configMock;

    /** @var PdoBackend */
    private $pdoBackendMock;

    public function setUp()
    {
        $this->configMock     = P::mock(Config::class);
        $this->pdoBackendMock = P::mock(PdoBackend::class);

        $this->target = new Db($this->configMock, $this->pdoBackendMock);
    }

    /**
     * @test
     */
    public function shouldReturnInsertId()
    {
        // Fixture
        P::when($this->pdoBackendMock)->insertId()->return(3);

        // Test
        $actual = $this->target->insertId();

        // Assert
        $this->assertEquals(3, $actual);
    }

    /**
     * @test
     */
    public function shouldEscapeAndRunQuery()
    {
        // Fixture
        $sql = 'SELECT * FROM names WHERE name = :name';
        $params = [ ':name' => 'Alice' ];

        P::when($this->pdoBackendMock)->escape('Alice')->return("'Alice'");
        P::when($this->pdoBackendMock)->query("SELECT * FROM names WHERE name = 'Alice'")->return(true);

        // Test
        $actual = $this->target->query($sql, $params);

        // Assert
        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function shouldReturnLastQuery()
    {
        // Fixture
        P::when($this->pdoBackendMock)->escape('Alice')->return("'Alice'");

        // Test
        $this->target->query('SELECT :name', [':name' => 'Alice']);
        $actual = $this->target->lastQuery();

        // Assert
        $this->assertEquals("SELECT 'Alice'", $actual);
    }
}
