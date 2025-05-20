<?php

namespace App\Tests\Unit\Deform;

use Deform\Version;

class VersionTest extends \Codeception\Test\Unit
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

    public function testVersion()
    {
        $gitVersions = Version::getGitVersions();
        $this->assertCount(2, $gitVersions);
    }

}