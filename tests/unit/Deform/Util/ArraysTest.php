<?php
namespace Deform\Util;

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

    public function testExtractKeysNonAssocLoose()
    {
        $result = Arrays::extractKeys([
            'one' => 'ONE',
            'two' => 'TWO',
            'three' => 'THREE',
            'four' => 'FOUR'
        ], [
            'one',
            'four',
            'missing'
        ]);
        $this->assertEquals(['one'=>'ONE','four'=>'FOUR','missing'=>null], $result);
    }

    public function testExtractKeysNonAssocStrictFail()
    {
        $this->expectException(\Exception::class);
        Arrays::extractKeys([
            'one' => 'ONE',
            'two' => 'TWO',
            'three' => 'THREE',
            'four' => 'FOUR'
        ], [
            'one',
            'four',
            'missing'
        ],true);
    }

    public function testExtractKeysAssocLoose()
    {
        $result = Arrays::extractKeys([
            'one' => 'ONE',
            'two' => 'TWO',
            'three' => 'THREE',
            'four' => 'FOUR'
        ], [
            'one'=>'One',
            'four'=>'Four',
            'missing'=>'Missing'
        ]);
        $this->assertEquals(['One'=>'ONE','Four'=>'FOUR','Missing'=>null], $result);
    }

    public function testExtractKeysAssocStrictFail()
    {
        $this->expectException(\Exception::class);
        Arrays::extractKeys([
            'one' => 'ONE',
            'two' => 'TWO',
            'three' => 'THREE',
            'four' => 'FOUR'
        ], [
            'one'=>'One',
            'four'=>'Four',
            'missing'=>'Missing'
        ], true);
    }
}
