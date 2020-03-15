<?php

namespace Basher\Tests;

use Basher\Command;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class CommandTest extends TestCase
{
    public function testWithOptionsArray()
    {
        $command = new Command('test', ['-f', '/path/to/file']);

        $this->assertEquals('test -f /path/to/file', $command->generateScript());
        $this->assertEquals(' && ', $command->getJoin());

        $process = $command->toProcess();

        $this->assertInstanceOf(Process::class, $process);
        $this->assertEquals("'test' '-f' '/path/to/file'", $process->getCommandLine());
    }

    public function testWithOptionsArrayAllowFail()
    {
        $command = new Command('test', ['-f', '/path/to/file'], [], true);

        $this->assertEquals('test -f /path/to/file', $command->generateScript());
        $this->assertEquals('; ', $command->getJoin());

        $process = $command->toProcess();

        $this->assertInstanceOf(Process::class, $process);
        $this->assertEquals("'test' '-f' '/path/to/file'", $process->getCommandLine());
    }

    public function testWithOptionsArrayAndEnvvars()
    {
        $command = new Command('test', ['-f', '/path/to/file'], ['PATH' => '/usr/local/bin:/usr/bin:/bin:/usr/local/sbin']);

        $this->assertEquals('PATH=/usr/local/bin:/usr/bin:/bin:/usr/local/sbin test -f /path/to/file', $command->generateScript());
        $this->assertEquals(' && ', $command->getJoin());

        $process = $command->toProcess();

        $this->assertInstanceOf(Process::class, $process);
        $this->assertEquals(['PATH' => '/usr/local/bin:/usr/bin:/bin:/usr/local/sbin'], $process->getEnv());
        $this->assertEquals("'test' '-f' '/path/to/file'", $process->getCommandLine());
    }

    public function testWithOptionsString()
    {
        $command = new Command('test', '-f /path/to/file');

        $this->assertEquals('test -f /path/to/file', $command->generateScript());
        $this->assertEquals(' && ', $command->getJoin());

        $process = $command->toProcess();

        $this->assertInstanceOf(Process::class, $process);
        $this->assertEquals("'test' '-f' '/path/to/file'", $process->getCommandLine());
    }
}
