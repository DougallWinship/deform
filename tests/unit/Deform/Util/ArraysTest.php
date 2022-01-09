<?php
namespace Deform\Util;

use function PHPUnit\Framework\isFalse;

class ArraysTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testIsAssoc()
    {
        $this->assertEquals(true, Arrays::isAssoc([]));
        $this->assertEquals(false, Arrays::isAssoc([
            0 => 'foo'
        ]));
        $this->assertEquals(false, Arrays::isAssoc([
            0 => 'foo',
            1 => 'bar'
        ]));
        // the following doesn't contain sequential keys ... let's be strict about things!
        $this->assertEquals(true, Arrays::isAssoc([
            1 => 'foo'
        ]));
        $this->assertEquals( true, Arrays::isAssoc([
            'foo' => 'bar'
        ]));
        $this->assertEquals(true, Arrays::isAssoc([
            0 => 'ahoy',
            'foo'=>'bar'
        ]));
    }
}
