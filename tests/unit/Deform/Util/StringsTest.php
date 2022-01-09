<?php
namespace Deform\Util;

class StringsTest extends \Codeception\Test\Unit
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

    public function testGetClassWithoutNamespace()
    {
        $classWithoutNamespace = Strings::getClassWithoutNamespace(Strings::class);
        $this->assertEquals("Strings", $classWithoutNamespace);
    }

    public function testSeparateCased()
    {
        $this->assertEquals("thisisatest", Strings::separateCased("Thisisatest"));
        $this->assertEquals("this_is_a_test", Strings::separateCased("ThisIsATest"));
        $this->assertEquals("this_is_a_test", Strings::separateCased("thisIsATest"));
        $this->assertEquals("this-is-a-test", Strings::separateCased("thisIsATest","-"));
        $this->assertEquals("this-is-a-test", Strings::separateCased("ThisIsATest","-"));
    }
}
