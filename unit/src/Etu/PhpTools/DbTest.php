<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
namespace Tests\Etu\PhpTools;

use \Etu\PhpTools\Db;
use \Etu\PhpTools\Config;
use \Etu\PhpTools\Db\PdoBackend;
use \Etu\PhpTools\Db\Raw as DbRaw;
use \Etu\PhpTools\Db\DbException;

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
        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()->getMock();
        $this->pdoBackendMock = $this->getMock(PdoBackend::class);

        $this->target = new Db($this->configMock, $this->pdoBackendMock);
    }

    /**
     * @test
     */
    public function shouldReturnInsertId()
    {
        // Fixture
        $this->pdoBackendMock->method('insertId')->will($this->returnValue(3));

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
        $params = [':name' => 'Alice', ':age'  => new DbRaw('20')];
        $escapedSql = "SELECT * FROM names WHERE name = 'Alice' AND age = 20";

        $this->pdoBackendMock->method('escape')->with('Alice')->will(
            $this->returnValue("'Alice'")
        );
        $this->pdoBackendMock->method('query')->with($escapedSql)->will(
            $this->returnValue($this->target)
        );

        // Test
        $actual = $this->target->query($sql, $params);

        // Assert
        $this->assertSame($this->target, $actual);
    }

    /**
     * @test
     * @expectedException Etu\PhpTools\Db\DbException
     */
    public function shouldFailQuery()
    {
        // Fixture
        $this->pdoBackendMock->method('query')->with('')->will(
            $this->throwException(new DbException())
        );

        // Test
        $this->target->query('');
    }

    /**
     * @test
     */
    public function shouldReturnLastQuery()
    {
        // Fixture
        $this->pdoBackendMock->method('escape')->with('Alice')->will(
            $this->returnValue("'Alice'")
        );
        $this->target->query('SELECT :name', [':name' => 'Alice']);

        // Test
        $actual = $this->target->lastQuery();

        // Assert
        $this->assertSame("SELECT 'Alice'", $actual);
    }

    /**
     * @test
     */
    public function shouldFetchRow()
    {
        // Fixture
        $row = (object) [ 'id' => 1, 'name' => 'Alice' ];
        $this->pdoBackendMock->method('fetchRow')->will($this->returnValue($row));

        // Test
        $actual = $this->target->fetchRow();

        // Assert
        $this->assertSame($row, $actual);
    }

    /**
     * @test
     */
    public function shouldFetchAll()
    {
        // Fixture
        $rows = [ (object) [ 'id' => 1, 'name' => 'Alice' ] ];
        $this->pdoBackendMock->method('fetchAll')->will($this->returnValue($rows));

        // Test
        $actual = $this->target->fetchAll();

        // Assert
        $this->assertSame($rows, $actual);
    }
}
