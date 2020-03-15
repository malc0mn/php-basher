<?php

namespace Basher\Tests;

use PHPUnit\Framework\TestCase;
use Basher\CommandStack;

class CommandStackTest extends TestCase
{
    /**
     * @var CommandStack
     */
    private $commandStack;

    public function setUp()
    {
        // Create anonymous class for testing instead of using
        // getMockForAbstractClass().
        $this->commandStack = new class extends CommandStack {
            public function executable($executable)
            {
                return parent::executable($executable);
            }

            public function stack($options, $allowFail = false, $executable = null, array $envVars = [])
            {
                return parent::stack($options, $allowFail, $executable, $envVars);
            }

            public function option($option, $optionArgument = '', $concat = ' ')
            {
                return parent::option($option, $optionArgument, $concat);
            }
        };
    }

    public function testAbstractConstruct()
    {
        $this->expectException('Error');
        $this->expectExceptionMessage('Cannot instantiate abstract class Basher\CommandStack');

        new CommandStack();
    }

    public function testSetFail()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("The option must start with a '-' or '+' sign!");

        $this->commandStack->set('e');
    }

    // TODO: somehow test (the effect of the) dir() method!

    public function testWorkingHasDirNoEffect()
    {
        $this->commandStack->dir('/path/to/working/dir');

        $this->assertEquals('', $this->commandStack->getStacked());
    }

    public function testSetBashOptions()
    {
        $this->commandStack->set('-e')
            ->set('-v')
        ;

        $this->assertEquals('set -e -v;', $this->commandStack->getStacked());
    }

    public function testRunNoExecutable()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You must add at least one command!');

        $this->commandStack->run();
    }

    public function testRunNoStackChained()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This command cannot be chained!');

        $this->commandStack
            ->executable('test')
            ->chained()
            ->run()
        ;
    }

    public function testDryrunChained()
    {
        $this->commandStack
            ->executable('test')
            ->chained()
        ;

        // Do not chain to prevent static code analysis warnings about method
        // access levels.
        $this->commandStack->stack(['-f', '/path/to/file']);

        $result = $this->commandStack->run(true);

        $this->assertTrue($result->wasDryrun());
        $this->assertTrue($result->wasSuccessful());
        $this->assertEquals(0, $result->getExitCode());
        $this->assertEquals('test -f /path/to/file', $result->getCommandline());
        $this->assertEquals('Dryrun: test -f /path/to/file would have been executed.', $result->getOutput());
    }

    public function testDryrunNoEscape()
    {
        // Do not chain to prevent static code analysis warnings about method
        // access levels.
        $this->commandStack->executable('test');
        $this->commandStack->stack(['-f', '/path/to/file']);

        $result = $this->commandStack->run(true);

        $this->assertTrue($result->wasDryrun());
        $this->assertTrue($result->wasSuccessful());
        $this->assertEquals(0, $result->getExitCode());
        $this->assertEquals('test -f /path/to/file', $result->getCommandline());
        $this->assertEquals('Dryrun: test -f /path/to/file would have been executed.', $result->getOutput());
    }

    public function testDryrunEscaped()
    {
        // Do not chain to prevent static code analysis warnings about method
        // access levels.
        $this->commandStack->executable('test');
        $this->commandStack->stack(['-f', '/path/to/file']);

        $result = $this->commandStack->run(true, true);

        $this->assertTrue($result->wasDryrun());
        $this->assertTrue($result->wasSuccessful());
        $this->assertEquals(0, $result->getExitCode());
        $this->assertEquals("'test' '-f' '/path/to/file'", $result->getCommandline());
        $this->assertEquals("Dryrun: 'test' '-f' '/path/to/file' would have been executed.", $result->getOutput());
    }

    public function testDryrunOptionNoEscape()
    {
        // Do not chain to prevent static code analysis warnings about method
        // access levels.
        $this->commandStack->executable('test');
        $this->commandStack->option('-f', '/path/to/file');

        $result = $this->commandStack->run(true);

        $this->assertTrue($result->wasDryrun());
        $this->assertTrue($result->wasSuccessful());
        $this->assertEquals(0, $result->getExitCode());
        $this->assertEquals('test -f /path/to/file', $result->getCommandline());
        $this->assertEquals('Dryrun: test -f /path/to/file would have been executed.', $result->getOutput());
    }

    public function testDryrunOptionEscape()
    {
        // Do not chain to prevent static code analysis warnings about method
        // access levels.
        $this->commandStack->executable('test');
        $this->commandStack->option('-f', '/path/to/file');

        $result = $this->commandStack->run(true, true);

        $this->assertTrue($result->wasDryrun());
        $this->assertTrue($result->wasSuccessful());
        $this->assertEquals(0, $result->getExitCode());
        $this->assertEquals("'test' '-f' '/path/to/file'", $result->getCommandline());
        $this->assertEquals("Dryrun: 'test' '-f' '/path/to/file' would have been executed.", $result->getOutput());
    }

    public function testRunOptionEscape()
    {
        $this->commandStack
            ->executable('test')
            ->dir('/tmp')
        ;

        // Do not chain to prevent static code analysis warnings about method
        // access levels.
        $this->commandStack->option('-f', '/path/to/file/that/just/cannot/exist');

        $result = $this->commandStack->run(false, true);

        $this->assertFalse($result->wasDryrun());
        $this->assertFalse($result->wasSuccessful());
        $this->assertEquals(1, $result->getExitCode());
        $this->assertEquals("'test' '-f' '/path/to/file/that/just/cannot/exist'", $result->getCommandline());
        $this->assertEquals("StdOut:\n\n\nStdErr:\n", $result->getOutput());
        $this->assertEquals(['StdOut:', '', '', 'StdErr:', ''], $result->getOutputAsArray());
    }

    public function testToString()
    {
        $this->commandStack
            ->executable('test')
            ->set('-e', '-v')
        ;

        // Do not chain to prevent static code analysis warnings about method
        // access levels.
        $this->commandStack->stack(['-f', '/path/to/file']);

        $expectedScript = "#!/bin/bash\n\nset -e -v\n\ntest -f /path/to/file\n";

        $this->assertEquals($expectedScript, $this->commandStack->generateScript());
        $this->assertEquals($expectedScript, $this->commandStack->prettyPrint());
        $this->assertEquals($expectedScript, (string) $this->commandStack);
    }
}
