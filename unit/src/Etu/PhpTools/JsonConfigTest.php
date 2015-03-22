<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2015
 */
namespace Tests\Etu\PhpTools;

use \Etu\PhpTools\JsonConfig as Config;

class JsonConfigTest extends \PHPUnit_Framework_TestCase
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
     * @expectedException Etu\PhpTools\ConfigException
     */
    public function shouldFailOnNonExistingFile()
    {
        new Config($this->configDir.'noSuchConfig.json');
    }

    /**
     * @test
     * @expectedException Etu\PhpTools\ConfigException
     */
    public function shouldFailOnNonMissingDefault()
    {
        new Config($this->configDir.'missingdefault.json');
    }

    /**
     * @test
     * @expectedException Etu\PhpTools\ConfigException
     */
    public function shouldFailOnEmptyConfig()
    {
        new Config($this->configDir.'emptyconfig.json');
    }

    /**
     * @test
     */
    public function shouldGetSimpleDefault()
    {
        // Fixture
        $target = new Config($this->configDir.'simple.json');

        // Test
        $actual = $target->match;

        // Assert
        $this->assertSame('default', $actual);
    }

    /**
     * @test
     */
    public function shouldGetSimpleExactMatching()
    {
        // Fixture
        $target = new Config($this->configDir.'simple.json', 'example.com');

        // Test
        $actual = $target->match;

        // Assert
        $this->assertSame('exact', $actual);
    }

    /**
     * @test
     */
    public function shouldGetSimpleFuzzyMatching()
    {
        // Fixture
        $target = new Config($this->configDir.'simple.json', 'fuzzy.example.com');

        // Test
        $actual = $target->match;

        // Assert
        $this->assertSame('fuzzy', $actual);
    }

    /**
     * @test
     */
    public function shouldGetRecursiveDefault()
    {
        // Fixture
        $target = new Config($this->configDir.'recursive.json');

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
        $target = new Config($this->configDir.'recursive.json', 'example.com');

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
    public function shouldHandleMultidimensionalKeys()
    {
        // Fixture
        $target = new Config($this->configDir.'deeperconfig.json');

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
