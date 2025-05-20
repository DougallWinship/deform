<?php
namespace App\Tests\Unit\Deform\Util;

use Deform\Exception\DeformUtilException;
use Deform\Util\Strings;

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
        $this->assertEquals("Strings", Strings::getClassWithoutNamespace(Strings::class));
        $this->assertEquals("Exception", Strings::getClassWithoutNamespace(\Exception::class));
        $this->assertEquals("stdClass", Strings::getClassWithoutNamespace(new \stdClass()));
    }

    public function testGetClassWithoutNamespaceFail1()
    {
        $this->expectException(DeformUtilException::class);
        Strings::getClassWithoutNamespace("not a class name");
    }

    public function testGetClassWithoutNamespaceFail2()
    {
        $this->expectException(DeformUtilException::class);
        Strings::getClassWithoutNamespace(1);// not an object or string
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

    public function testTrimInternal()
    {
        $this->assertEquals('this is a test', Strings::trimInternal('this is a test'));
        $this->assertEquals('this is a test', Strings::trimInternal('  this is a test'));
        $this->assertEquals('this is a test', Strings::trimInternal('this is a test  '));
        $this->assertEquals('this is a test', Strings::trimInternal('   this is a test  '));
        $this->assertEquals('this is a test', Strings::trimInternal('this   is   a   test'));
        $this->assertEquals('this is a test', Strings::trimInternal('    this   is   a   test'));
        $this->assertEquals('this is a test', Strings::trimInternal('this   is   a   test    '));
        $this->assertEquals('this is a test', Strings::trimInternal('     this   is   a   test    '));
    }

    public function testExtractStaticMethodSignature()
    {
        $this->assertNull(Strings::extractStaticMethodSignature("no * at start"));

        $this->assertNull(Strings::extractStaticMethodSignature(" * @method static foo bar"));

        $this->assertEquals([
            'className'=>'HtmlTag',
            'methodName'=>'a',
            'params'=>'array $attributes=[]',
            'comment'=>'',
        ],Strings::extractStaticMethodSignature(' * @method static HtmlTag a(array $attributes=[])'));

        $this->assertEquals([
            'className'=>'HtmlTag',
            'methodName'=>'a',
            'params'=>'array $attributes=[]',
            'comment'=>'method comment',
            'comment_parts' => ['method','comment']
        ],Strings::extractStaticMethodSignature(' * @method static HtmlTag a(array $attributes=[]) method comment'));
    }

    public function testPrependPerLine()
    {
        $arr = <<<TEST
this is simple some text to test
it's pretty dull to be fair
  this is slightly indented already
TEST;
        $prepend = "1234";
        $result  = Strings::prependPerLine($arr,$prepend);
        $resultParts = explode(PHP_EOL, $result);
        foreach ($resultParts as $part) {
            $this->assertStringStartsWith($prepend, $part);
        }
    }
}
