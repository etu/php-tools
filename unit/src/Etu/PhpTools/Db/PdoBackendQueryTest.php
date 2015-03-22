<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
namespace Tests\Etu\PhpTools\Db;

use \Etu\PhpTools\Db\PdoBackend;
use \Etu\PhpTools\Config;

class PdoBackendQueryTest extends \PHPUnit_Framework_TestCase
{
    /** @var PdoBackend */
    private $target;

    /** @var Config */
    private $configMock;

    public function setUp()
    {
        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()->getMock();
        $this->configMock->method('__get')->with('db')->will($this->returnValue(
            (object) [
                'dsn' => 'sqlite::memory:',
                'username' => '',
                'password' => ''
            ]
        ));

        $this->target = new PdoBackend();
        $this->target->connect($this->configMock);

        $this->target->query('CREATE TABLE names(id INTEGER PRIMARY KEY, name VARCHAR(10));');
        $this->target->query('INSERT INTO names(id, name) VALUES (NULL, "Alice");');
    }

    /**
     * @test
     */
    public function shouldNotFailQuery()
    {
        // Fixture
        // Test
        $actual = $this->target->query('SELECT * FROM names WHERE name = "Alice"');

        // Assert
        $this->assertTrue($actual);
    }

    /**
     * @test
     * @expectedException Etu\PhpTools\Db\DbException
     */
    public function shouldFailQuery()
    {
        // Test
        $actual = $this->target->query('DELETE "Someone else"');
    }

    /**
     * @test
     */
    public function shouldReturnInsertId()
    {
        // Fixture
        $this->target->query('INSERT INTO names(id, name) VALUES (NULL, "Bob");');

        // Test
        $actual = $this->target->insertId();

        // Assert
        $this->assertSame(2, $actual);
    }

    /**
     * @test
     */
    public function shouldEscapeString()
    {
        // Fixture
        // Test
        $actual = $this->target->escape('"; SELECT "Alice";');

        // Assert
        $this->assertSame("'\"; SELECT \"Alice\";'", $actual);
    }

    /**
     * @test
     */
    public function shouldFetchRow()
    {
        // Fixture
        $this->target->query('SELECT * FROM names');

        // Test
        $actual = $this->target->fetchRow();

        // Assert
        $this->assertEquals(
            (object) [
                'id'   => 1,
                'name' => 'Alice'
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldFetchAll()
    {
        // Fixture
        $this->target->query('SELECT * FROM names');

        // Test
        $actual = $this->target->fetchAll();

        // Assert
        $this->assertEquals(
            [
                (object) [
                    'id'   => 1,
                    'name' => 'Alice'
                ]
            ],
            $actual
        );
    }
}
