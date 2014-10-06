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
        $this->goodConfig = $this->configDir.'goodconfig.ini';
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
        $target = new Config($this->configDir.'nondefaultarray.ini', 'example.com');

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
        $actual = $target->db->db1->dsn;

        // Assert
        $this->assertSame('sqlite::memory:', $actual);
    }
}
