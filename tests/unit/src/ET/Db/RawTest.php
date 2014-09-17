<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
namespace Tests\ET\Db;

use \ET\Db\Raw;

use \Phockito as P;
use \Hamcrest_Matchers as H;

class RawTest extends \PHPUnit_Framework_TestCase
{
    /** @var Raw */
    private $target;

    public function setUp()
    {
        $this->target = new Raw('1234');
    }

    /**
     * @test
     */
    public function shouldReturnRaw()
    {
        // Fixture
        // Test
        $actual = (string) $this->target;

        // Assert
        $this->assertEquals('1234', $actual);
    }
}
