<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
namespace Tests\ET;

use \ET\Db;
use \ET\Config;
use \ET\Db\PdoBackend;
use \ET\Db\Raw as DbRaw;

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
        $this->assertSame(3, $actual);
    }

    /**
     * @test
     */
    public function shouldEscapeAndRunQuery()
    {
        // Fixture
        $sql = 'SELECT * FROM names WHERE name = :name AND age = :age';
        $params = [
            ':name' => 'Alice',
            ':age'  => new DbRaw('20')
        ];

        P::when($this->pdoBackendMock)->escape('Alice')->return("'Alice'");
        P::when($this->pdoBackendMock)->query("SELECT * FROM names WHERE name = 'Alice' AND age = 20")->return(true);

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
        $this->assertSame("SELECT 'Alice'", $actual);
    }
}
