<?php

namespace Basher\Tests;

use Basher\Tools\OneLiner;
use PHPUnit\Framework\TestCase;

class OneLinerTest extends TestCase
{
    /**
     * @var OneLiner
     */
    protected $oneLiner;

    protected function setUp()
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

    public function testPrependCmd()
    {
        $this->oneLiner
            ->addCmd('cat', '/tmp/example/script.sh')
            ->prependCmd('source', '/path/to/some/envvarsfile')
        ;

        $this->assertEquals('source /path/to/some/envvarsfile && cat /tmp/example/script.sh', $this->oneLiner->getStacked());
    }
}
