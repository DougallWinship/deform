<?php
namespace App\Tests\Unit\Deform\Html;

use Deform\Exception\DeformHtmlException;
use Deform\Html\Link;

class LinkTest extends \Codeception\Test\Unit
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
    public function testStaticLinkGenerationSecure()
    {
        $link = Link::url("https://github.com/DougallWinship/deform");
        $this->assertInstanceOf(Link::class, $link);
        $html = (string)$link;
        $this->assertEquals("<a href='https://github.com/DougallWinship/deform'>https://github.com/DougallWinship/deform</a>",$html);
    }

    public function testStaticLinkGenerationInsecure()
    {
        $link = Link::url("http://github.com/DougallWinship/deform");
        $this->assertInstanceOf(Link::class, $link);
        $html = (string)$link;
        $this->assertEquals("<a href='http://github.com/DougallWinship/deform'>http://github.com/DougallWinship/deform</a>",$html);
    }


    public function testSetUrl()
    {
        $link = new Link();
        $link->setUrl("https://github.com/DougallWinship/deform?foo=bar#readme");
        $urlParts = $this->tester->getAttributeValue($link,'urlParts');
        $this->assertEquals([
            'scheme' => 'https',
            'host' => 'github.com',
            'path' => '/DougallWinship/deform',
            'query' => 'foo=bar',
            'fragment' => 'readme'
        ], $urlParts);
    }

    public function testBadSetUrl()
    {
        $this->expectException(DeformHtmlException::class);
        $link = new Link();
        $link->setUrl('///////aaaaagh//////');
    }
    
    public function testFullLinkGeneration() 
    {
        $link = new Link();
        $link
            ->setHost("github.com")
            ->setScheme("http")
            ->setPort("80")
            ->setUser("dougall","password")
            ->setPath("/DougallWinship/deform")
            ->setQuery("foo=bar")
            ->setFragment("readme")
            ->text("deform");
        $html = (string)$link;
        $this->assertEquals("<a href='http://dougall:password@github.com:80/DougallWinship/deform?foo=bar#readme'>deform</a>", $html);
    }

    public function testLinkGenerationNoPassword()
    {
        $link = new Link();
        $link
            ->setHost("github.com")
            ->setScheme("http")
            ->setPort("80")
            ->setUser("dougall")
            ->setPath("/DougallWinship/deform")
            ->setQuery("foo=bar")
            ->setFragment("readme")
            ->text("deform");
        $html = (string)$link;
        $this->assertEquals("<a href='http://dougall@github.com:80/DougallWinship/deform?foo=bar#readme'>deform</a>", $html);
    }

    public function testLinkGenerationNoScheme()
    {
        $link = new Link();
        $link
            ->setHost("github.com")
            ->setPort("80")
            ->setUser("dougall")
            ->setPath("/DougallWinship/deform")
            ->setQuery("foo=bar")
            ->setFragment("readme")
            ->text("deform");
        $html = (string)$link;
        $this->assertEquals("<a href='https://dougall@github.com:80/DougallWinship/deform?foo=bar#readme'>deform</a>", $html);
    }

    public function testLinkGenerationNoPath()
    {
        $link = new Link();
        $link
            ->setScheme("http")
            ->setHost("github.com")
            ->setPort("80")
            ->setUser("dougall","password")
            ->setQuery("foo=bar")
            ->setFragment("readme")
            ->text("deform");
        $html = (string)$link;
        $this->assertEquals("<a href='http://dougall:password@github.com:80?foo=bar#readme'>deform</a>", $html);
    }

    public function testLinkGenerationNoHostException()
    {
        $link = new Link();
        $link
            ->setScheme("http")
            ->setPort("80")
            ->setUser("dougall","password")
            ->setQuery("foo=bar")
            ->setFragment("readme")
            ->text("deform");
        $this->expectException(DeformHtmlException::class);
        $html = (string)$link;
    }

    public function testSetProtocolAlias()
    {
        $link = Link::url("https://github.com/DougallWinship/deform");
        $link->setProtocol("http");
        $this->assertInstanceOf(Link::class, $link);
        $html = (string)$link;
        $this->assertEquals("<a href='http://github.com/DougallWinship/deform'>http://github.com/DougallWinship/deform</a>",$html);
    }

}