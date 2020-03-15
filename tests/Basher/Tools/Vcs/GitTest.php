<?php

namespace Basher\Tests\Tools\Vcs;

use Basher\Tools\Vcs\Git;
use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    /**
     * @var Git
     */
    private $git;

    public function setUp()
    {
        $this->git = new Git();
    }

    public function testShallowClone()
    {
        $this->git->clone('https://my.com/repo.git');

        $this->assertEquals('git clone --depth 1 https://my.com/repo.git', $this->git->getStacked());
    }

    public function testRegularClone()
    {
        $this->git->clone('https://my.com/repo.git', '', '', false);

        $this->assertEquals('git clone https://my.com/repo.git', $this->git->getStacked());
    }

    public function testRegularCloneToDestination()
    {
        $this->git->clone('https://my.com/repo.git', '/path/to/dest', '', false);

        $this->assertEquals('git clone https://my.com/repo.git /path/to/dest', $this->git->getStacked());
    }

    public function testRegularCloneToDestinationOfBranch()
    {
        $this->git->clone('https://my.com/repo.git', '/path/to/dest', 'develop', false);

        $this->assertEquals('git clone -b develop https://my.com/repo.git /path/to/dest', $this->git->getStacked());
    }

    public function testAdd()
    {
        $this->git->add('-A');

        $this->assertEquals('git add -A', $this->git->getStacked());
    }

    public function testCommit()
    {
        $this->git->commit('Initial commit');

        $this->assertEquals("git commit -m 'Initial commit'", $this->git->getStacked());
    }

    public function testCommitWithOptions()
    {
        $this->git->commit('Initial commit', '-n');

        $this->assertEquals("git commit -m 'Initial commit' -n", $this->git->getStacked());
    }

    public function testPull()
    {
        $this->git->pull();

        $this->assertEquals('git pull', $this->git->getStacked());
    }

    public function testPullOriginMaster()
    {
        $this->git->pull('origin', 'master');

        $this->assertEquals('git pull origin master', $this->git->getStacked());
    }

    public function testPush()
    {
        $this->git->push();

        $this->assertEquals('git push', $this->git->getStacked());
    }

    public function testPushOriginMaster()
    {
        $this->git->push('origin', 'master');

        $this->assertEquals('git push origin master', $this->git->getStacked());
    }

    public function testMerge()
    {
        $this->git->merge('master');

        $this->assertEquals('git merge master', $this->git->getStacked());
    }

    public function testMergeWithOptions()
    {
        $this->git->merge('master', '--no-ff');

        $this->assertEquals('git merge --no-ff master', $this->git->getStacked());
    }

    public function testCheckout()
    {
        $this->git->checkout('develop');

        $this->assertEquals('git checkout develop', $this->git->getStacked());
    }

    public function testTag()
    {
        $this->git->tag('v1.0.3');

        $this->assertEquals('git tag v1.0.3', $this->git->getStacked());
    }

    public function testTagWithMessage()
    {
        $this->git->tag('v1.0.3', 'Fixed some bugs');

        $this->assertEquals("git tag -m 'Fixed some bugs' v1.0.3", $this->git->getStacked());
    }
}
