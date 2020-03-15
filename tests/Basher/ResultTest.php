<?php

namespace Basher\Tests;

use Basher\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function testFailedRealResult()
    {
        $result = new Result('test -f /path/to/file', 1, "This is the\noutput");

        $this->assertEquals('test -f /path/to/file', $result->getCommandline());
        $this->assertEquals(1, $result->getExitCode());
        $this->assertFalse($result->wasSuccessful());
        $this->assertEquals("This is the\noutput", $result->getOutput());
        $this->assertEquals(['This is the', 'output'], $result->getOutputAsArray());
        $this->assertFalse($result->wasDryrun());
    }

    public function testSuccessfulDryrunResult()
    {
        $result = new Result('test -f /path/to/file', 0, "This is the\noutput", true);

        $this->assertEquals('test -f /path/to/file', $result->getCommandline());
        $this->assertEquals(0, $result->getExitCode());
        $this->assertTrue($result->wasSuccessful());
        $this->assertEquals("This is the\noutput", $result->getOutput());
        $this->assertEquals(['This is the', 'output'], $result->getOutputAsArray());
        $this->assertTrue($result->wasDryrun());
    }
}
