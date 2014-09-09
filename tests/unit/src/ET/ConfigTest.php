<?php
namespace Tests\ET;

use \ET\Config;

use \Phockito as P;
use \Hamcrest_Matchers as H;

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
    public function shouldLoadConfig()
    {
        // Fixture
        $target = new Config($this->goodConfig, 'example.com');

        // Test
        $actual = $target->dumpConfig();

        // Assert
        $this->assertTrue(is_array($actual));
    }

    /**
     * @test
     */
    public function shouldGiveDefaultConfig()
    {
        // Fixture
        $target = new Config($this->goodConfig, 'example.nu');

        // Test
        $actual = $target->dumpConfig();

        // Assert
        $this->assertEquals(
            [
                'db' => [
                    'hostname' => 'localhost',
                    'username' => 'username',
                    'password' => 'password',
                    'database' => 'database'
                ]
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldGiveExactMatchingConfig()
    {
        // Fixture
        $target = new Config($this->goodConfig, 'example.com');

        // Test
        $actual = $target->dumpConfig();

        // Assert
        $this->assertEquals(
            [
                'db' => [
                    'hostname' => 'localhost',
                    'username' => 'example',
                    'password' => 'password',
                    'database' => 'database'
                ],
                'theme' => 'default'
            ],
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldGiveFuzzyMatchingConfig()
    {
        // Fixture
        $target = new Config($this->goodConfig, 'fuzzy.example.com');

        // Test
        $actual = $target->dumpConfig();

        // Assert
        $this->assertEquals(
            [
                'db' => [
                    'hostname' => 'fuzzyMatching',
                    'username' => 'username',
                    'password' => 'password',
                    'database' => 'database'
                ],
                'theme' => 'fuzzy'
            ],
            $actual
        );
    }
}
