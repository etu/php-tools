<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
namespace Tests\Etu\PhpTools\Db;

use \Etu\PhpTools\Db\Raw;

class RawTest extends \PHPUnit_Framework_TestCase
{
    /** @var Raw */
    private $target1;

    /** @var Raw */
    private $target2;

    public function setUp()
    {
        $this->target1 = new Raw('1234');
        $this->target2 = new Raw(1234);
    }

    /**
     * @test
     */
    public function shouldReturnRawString()
    {
        // Fixture
        // Test
        $actual = $this->target1->__toString();

        // Assert
        $this->assertSame('1234', $actual);
    }

    /**
     * @test
     */
    public function shouldReturnRawIntegerAsString()
    {
        // Fixture
        // Test
        $actual = $this->target2->__toString();

        // Assert
        $this->assertSame('1234', $actual);
    }
}
