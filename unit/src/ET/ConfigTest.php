<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
namespace Tests\ET;

use \ET\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    private $target;

    private $goodConfig;

    public function setUp()
    {
        $this->goodConfig = __DIR__.'/files/goodconfig.ini';
    }

    /**
     * @test
     * @expectedException ET\ConfigException
     */
    public function shouldFailOnNonExistingFile()
    {
        new Config(__DIR__.'/files/noSuchConfig.ini', 'example.com');
    }

    /**
     * @test
     * @expectedException ET\ConfigException
     */
    public function shouldFailOnEmptyConfig()
    {
        new Config(__DIR__.'/files/emptyconfig.ini', 'example.com');
    }

    /**
     * @test
     */
    public function shouldGetDefaultRecursive()
    {
        // Fixture
        $target = new Config($this->goodConfig, 'test.example.net');

        // Test
        $actual = $target->db;

        // Assert
        $this->assertEquals(
            (object) [
                'dsn' => 'sqlite::memory:',
                'username' => 'username',
                'password' => 'password',
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldGetExactMatchNonRecursive()
    {
        // Fixture
        $target = new Config($this->goodConfig, 'example.com');

        // Test
        $actual = $target->theme;

        // Assert
        $this->assertSame('default', $actual);
    }

    /**
     * @test
     */
    public function shouldGetExactMatchRecursive()
    {
        // Fixture
        $target = new Config($this->goodConfig, 'example.com');

        // Test
        $actual = $target->db;

        // Assert
        $this->assertEquals(
            (object) [
                'dsn' => 'sqlite::memory:',
                'username' => 'example',
                'password' => 'password',
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldGetFuzzyMatchNonRecursive()
    {
        // Fixture
        $target = new Config($this->goodConfig, 'fuzzy.example.com');

        // Test
        $actual = $target->theme;

        // Assert
        $this->assertSame('fuzzy', $actual);
    }

    /**
     * @test
     */
    public function shouldGetFuzzyMatchRecursive()
    {
        // Fixture
        $target = new Config($this->goodConfig, 'fuzzy.example.com');

        // Test
        $actual = $target->db;

        // Assert
        $this->assertEquals(
            (object) [
                'dsn' => 'sqlite::memory:',
                'username' => 'fuzzy',
                'password' => 'password',
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldHandleArraysThatIsNotSetInGlobalConfig()
    {
        // Fixture
        $target = new Config(__DIR__.'/files/nondefaultarray.ini', 'example.com');

        // Test
        $actual = $target->db->dsn;

        // Assert
        $this->assertSame('sqlite::memory:', $actual);
    }

    /**
     * @test
     */
    public function shouldHandleMultidimensionalKeys()
    {
        // Fixture
        $target = new Config(__DIR__.'/files/deeperconfig.ini', 'example.com');

        // Test
        $actual = $target->db->db1->dsn;

        // Assert
        $this->assertSame('sqlite::memory:', $actual);
    }
}