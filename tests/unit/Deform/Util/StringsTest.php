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

    public function testCamelise()
    {
        $this->assertEquals("Thisisatest", Strings::camelise("thisisatest"));
        $this->assertEquals("ThisIsATest", Strings::camelise("this_is_a_test"));
        $this->assertEquals("ThisIsATest", Strings::camelise("___this_is_a_test"));
        $this->assertEquals("ThisIsATest", Strings::camelise("this_is_a_test____"));
        $this->assertEquals("ThisIsATest", Strings::camelise("this___is______a_test"));
    }
}
