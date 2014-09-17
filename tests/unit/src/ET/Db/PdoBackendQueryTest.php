<?php
namespace Tests\ET\Db;

use \ET\Db\PdoBackend;
use \ET\Config;

use \Phockito as P;
use \Hamcrest_Matchers as H;

class PdoBackendQueryTest extends \PHPUnit_Framework_TestCase
{
    /** @var PdoBackend */
    private $target;

    /** @var Config */
    private $configMock;

    public function setUp()
    {
        $this->configMock = P::mock(Config::class);
        P::when($this->configMock)->__get('db')->return(
            (object) [
                'dsn' => 'sqlite::memory:',
                'username' => 'root',
                'password' => ''
            ]
        );

        $this->target = new PdoBackend($this->configMock);
        $this->target->connect();

        $this->target->query('CREATE TABLE names(id INTEGER PRIMARY KEY, name VARCHAR(10));');
        $this->target->query('INSERT INTO names(id, name) VALUES (NULL, "Alice");');
    }

    /**
     * @test
     */
    public function shouldReturnData()
    {
        // Fixture
        // Test
        $actual = $this->target->query('SELECT * FROM names WHERE name = "Alice"');

        // Assert
        $this->assertEquals(
            (object) [
                'result' => [
                    (object) [
                        'id'   => '1',
                        'name' => 'Alice'
                    ]
                ],
                'rows' => 0
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldNotReturnData()
    {
        // Fixture
        // Test
        $actual = $this->target->query('DELETE "Someone else"');

        // Assert
        $this->assertFalse($actual);
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
        $this->assertEquals(2, $actual);
    }

    /**
     * @test
     */
    public function shouldEscapeString()
    {
        // Fixture
        // Test
        $actual = $this->target->escape('"; SELECT "Alice";');

        // Actual
        $this->assertEquals("'\"; SELECT \"Alice\";'", $actual);
    }
}
