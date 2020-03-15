<?php

namespace Basher\Tests\Tools;

use Basher\Tools\OneLiner;
use PHPUnit\Framework\TestCase;

class OneLinerTest extends TestCase
{
    /**
     * @var OneLiner
     */
    protected $oneLiner;

    protected function setUp(): void
    {
        $this->oneLiner = new OneLiner();
    }

    public function testAddCmd()
    {
        $this->oneLiner
            ->addCmd('source', '/path/to/some/envvarsfile')
            ->addCmd('cat', '/tmp/example/script.sh')
        ;

        $this->assertEquals('source /path/to/some/envvarsfile && cat /tmp/example/script.sh', $this->oneLiner->getStacked());
    }

    public function testAddCmdAllowFail()
    {
        $this->oneLiner
            ->addCmd('source', '/path/to/some/envvarsfile', true)
            ->addCmd('cat', '/tmp/example/script.sh', true)
        ;

        $this->assertEquals('source /path/to/some/envvarsfile; cat /tmp/example/script.sh', $this->oneLiner->getStacked());
    }

    public function testAddCmdMixed()
    {
        $this->oneLiner
            ->addCmd('source', '/path/to/some/envvarsfile', true)
            ->addCmd('cat', '/tmp/example/script.sh')
            ->addCmd('touch', '/tmp/example/script.sh')
        ;

        $this->assertEquals('source /path/to/some/envvarsfile; cat /tmp/example/script.sh && touch /tmp/example/script.sh', $this->oneLiner->getStacked());
    }

    public function testAddCmdWithEnvVars()
    {
        $this->oneLiner
            ->addCmd('source', '/path/to/some/envvarsfile')
            ->addCmd('cat', '/tmp/example/script.sh', false, ['HELLO' => 'world']);
        ;

        $this->assertEquals('source /path/to/some/envvarsfile && HELLO=world cat /tmp/example/script.sh', $this->oneLiner->getStacked());
    }

    public function testAddCmdAllowFailWithEnvVars()
    {
        $this->oneLiner
            ->addCmd('source', '/path/to/some/envvarsfile', true)
            ->addCmd('cat', '/tmp/example/script.sh', true, ['HELLO' => 'world']);
        ;

        $this->assertEquals('source /path/to/some/envvarsfile; HELLO=world cat /tmp/example/script.sh', $this->oneLiner->getStacked());
    }

    public function testPrependCmd()
    {
        $this->oneLiner
            ->addCmd('cat', '/tmp/example/script.sh')
            ->prependCmd('source', '/path/to/some/envvarsfile')
        ;

        $this->assertEquals('source /path/to/some/envvarsfile && cat /tmp/example/script.sh', $this->oneLiner->getStacked());
    }

    public function testPrependCmdAllowFail()
    {
        $this->oneLiner
            ->addCmd('cat', '/tmp/example/script.sh', true)
            ->prependCmd('source', '/path/to/some/envvarsfile', true)
        ;

        $this->assertEquals('source /path/to/some/envvarsfile; cat /tmp/example/script.sh', $this->oneLiner->getStacked());
    }

    public function testPrependCmdWithEnvVars()
    {
        $this->oneLiner
            ->addCmd('cat', '/tmp/example/script.sh', false, ['HELLO' => 'world'])
            ->prependCmd('source', '/path/to/some/envvarsfile')
        ;

        $this->assertEquals('source /path/to/some/envvarsfile && HELLO=world cat /tmp/example/script.sh', $this->oneLiner->getStacked());
    }

    public function testPrependCmdAllowFailWithEnvVars()
    {
        $this->oneLiner
            ->addCmd('cat', '/tmp/example/script.sh', true, ['HELLO' => 'world'])
            ->prependCmd('source', '/path/to/some/envvarsfile', true)
        ;

        $this->assertEquals('source /path/to/some/envvarsfile; HELLO=world cat /tmp/example/script.sh', $this->oneLiner->getStacked());
    }
}
