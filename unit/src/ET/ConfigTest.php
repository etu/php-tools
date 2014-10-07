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

    private $configDir;
    private $goodConfig;

    public function setUp()
    {
        $this->configDir  = __DIR__.'/files/';
    }

    /**
     * @test
     * @expectedException ET\ConfigException
     */
    public function shouldFailOnNonExistingFile()
    {
        new Config($this->configDir.'noSuchConfig.ini', 'example.com');
    }

    /**
     * @test
     * @expectedException ET\ConfigException
     */
    public function shouldFailOnEmptyConfig()
    {
        new Config($this->configDir.'emptyconfig.ini', 'example.com');
    }

    /**
     * @test
     */
    public function shouldGetSimpleDefault()
    {
        // Fixture
        $target = new Config($this->configDir.'simple.ini');

        // Test
        $actual = $target->theme;

        // Assert
        $this->assertSame('default', $actual);
    }

    /**
     * @test
     */
    public function shouldGetSimpleExactMatching()
    {
        // Fixture
        $target = new Config($this->configDir.'simple.ini', 'example.com');

        // Test
        $actual = $target->theme;

        // Assert
        $this->assertSame('exact', $actual);
    }

    /**
     * @test
     */
    public function shouldGetSimpleFuzzyMatching()
    {
        // Fixture
        $target = new Config($this->configDir.'simple.ini', 'fuzzy.example.com');

        // Test
        $actual = $target->theme;

        // Assert
        $this->assertSame('fuzzy', $actual);
    }

    /**
     * @test
     */
    public function shouldGetRecursiveDefault()
    {
        // Fixture
        $target = new Config($this->configDir.'recursive.ini');

        // Test
        $actual = $target->db;

        // Assert
        $this->assertEquals(
            (object) [
                'dsn' => 'sqlite::memory:',
                'username' => 'default',
                'password' => 'default'
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldGetRecursiveMatching()
    {
        // Fixture
        $target = new Config($this->configDir.'recursive.ini', 'example.com');

        // Test
        $actual = $target->db;

        // Assert
        $this->assertEquals(
            (object) [
                'dsn' => 'sqlite::memory:',
                'username' => 'live',
                'password' => 'live'
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldGetNonDefaultObject()
    {
        // Fixture
        $target = new Config($this->configDir.'nondefaultobject.ini', 'example.com');

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
        $target = new Config($this->configDir.'deeperconfig.ini', 'example.com');

        // Test
        $actual = $target->names->list;

        // Assert
        $this->assertEquals(
            (object) [
                'name1' => 'Alice',
                'name2' => 'Bob',
                'name3' => 'Claire'
            ],
            $actual
        );
    }
}
