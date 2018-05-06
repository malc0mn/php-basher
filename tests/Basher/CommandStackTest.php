<?php

namespace Basher\Tests;

use PHPUnit\Framework\TestCase;
use Basher\CommandStack;

class CommandStackTest extends TestCase
{
    public function testAbstractConstruct()
    {
        $this->expectException('Error');
        $this->expectExceptionMessage('Cannot instantiate abstract class Basher\CommandStack');

        $cmdStack = new CommandStack();
    }

    public function testSetFail()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage("The option must start with a '-' or '+' sign!");

        /** @var CommandStack $stub */
        $stub = $this->getMockForAbstractClass('Basher\CommandStack');

        $stub->set('e');
    }
}
