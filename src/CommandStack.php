<?php

/**
 * @file
 *
 * At some point in time, this was Based on the Robo PHP task runner.
 *
 * @see https://github.com/codegyre/robo/
 */

namespace Basher;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

abstract class CommandStack
{
    const SHEBANG = '#!/bin/bash';

    /**
     * @var string The main CLI executable to call.
     */
    protected $executable;

    /**
     * @var string The directory to run in.
     */
    protected $workingDirectory;

    /**
     * @var array Settings that change shell and/or script behavior.
     */
    protected $bashOptions = [];

    /**
     * @var Command[] A (set of) command(s) that will be run.
     */
    protected $stack = [];

    /**
     * @var array A set of options with their possible arguments to pass to the
     *            command.
     */
    protected $options = [];

    /**
     * @var array A set of arguments to append to the command after the options.
     */
    protected $arguments = [];

    /**
     * @var bool Defines the way of execution: concatenated with ampersands or
     *           each command separately.
     */
    protected $chained = false;

    /**
     * @var array
     */
    protected $allowedConcats = ['', ' ', '='];

    /**
     * @param $executable
     *
     * @return static
     */
    protected function executable($executable)
    {
        $this->executable = $executable;
        return $this;
    }

    /**
     * Changes the working directory of the command.
     *
     * @param $dir
     *
     * @return static
     */
    public function dir($dir)
    {
        $this->workingDirectory = $dir;
        return $this;
    }

    /**
     * Set options to change the behavior of the shell.
     *
     * @param string ...$options
     *
     * @return static
     *
     * @see http://tldp.org/LDP/abs/html/options.html
     *
     */
    public function set(...$options)
    {
        foreach ($options as $option) {
            if (!in_array($option[0], ['+', '-'])) {
                throw new \RuntimeException("The option must start with a '-' or '+' sign!");
            }
            $this->bashOptions[] = $option;
        }
        return $this;
    }

    /**
     * Get the specified bash options.
     *
     * @param bool $array
     * @param string $eol The end of line character for the 'set' options
     *
     * @return array|string
     */
    private function getBashOptions($array = false, $eol = ';')
    {
        if (!$array) {
            $string = '';
            if (!empty($this->bashOptions)) {
                $string = 'set ' . implode(' ', $this->bashOptions) . $eol;
            }
            return $string;
        }

        return $this->bashOptions;
    }

    /**
     * Push a command to the command execution stack.
     *
     * @param string|array $options
     * @param bool $allowFail When the command will be executed as 'chained',
     *                        this particular part of the stack will be added to
     *                        the chain using a trailing semi-colon (;) so that
     *                        any of the following commands will be executed
     *                        regardless of the outcome of the previous command.
     * @param string|null $executable Override the executable set in the
     *                                'executable' property
     * @param array $envVars An associative array with the variable name as key
     *                       and the value as, wait for it, the value.
     *
     * @return static
     */
    protected function stack(
        $options,
        $allowFail = false,
        $executable = null,
        array $envVars = []
    ) {
        $this->stack[] = new Command(
            $executable ?: $this->executable,
            $options,
            $envVars,
            $allowFail
        );

        return $this;
    }

    /**
     * Add options with their possible arguments to the options stack.
     *
     * @param string $option
     * @param string|int $optionArgument
     * @param string $concat space|=
     *
     * @return static
     *
     * @throws \RuntimeException
     */
    protected function option($option, $optionArgument = '', $concat = ' ')
    {
        if (!in_array($concat, $this->allowedConcats)) {
            throw new \RuntimeException(sprintf(
                "The concat value must be one of '%s'!", implode("', '", $this->allowedConcats)
            ));
        }

        if ($concat === ' ') {
            $this->options[] = $option;
            if ($optionArgument != '') {
                $this->options[] = $optionArgument;
            }
        } else {
            $this->options[] = ($optionArgument == '') ? $option : $option . $concat . $optionArgument;
        }

        return $this;
    }

    /**
     * Add arguments to the arguments stack.
     * They will get added AFTER the options.
     *
     * @param $argument
     */
    protected function argument($argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * Return all of the commands to be executed as a 'oneliner' string or as an
     * array.
     *
     * @param bool $asArray Set to true to return the command stack as an array
     *                      where each element is a ready to be executed string.
     *
     * @return array|string
     */
    public function getStacked($asArray = false)
    {
        $last = count($this->stack) - 1;

        $chain = [];
        foreach ($this->stack as $key => $command) {
            $script = $command->generateScript();

            // Add join string unless its the last item.
            if (!$asArray && $key !== $last) {
                $script .= $command->getJoin();
            }
            $chain[] = $script;
        }

        if (!$asArray) {
            return $this->getBashOptions() . implode('', $chain);
        }

        return array_merge($this->getBashOptions(true), $chain);
    }

    /**
     * @param bool $chained
     *
     * @return static
     */
    public function chained($chained = true)
    {
        $this->chained = $chained;
        return $this;
    }

    /**
     * Run the command.
     *
     * @param bool $dryrun When set to true, no commands will be executed, only
     *                     printed.
     * @param bool $raw When set to true, no escaping will be performed!
     * @param bool $splitOutput When set to false, you must make sure that
     *                          stdErr and stdOut are returned in a single
     *                          stream.
     *
     * @return Result
     *
     * @throws \RuntimeException
     */
    public function run($dryrun = false, $raw = true, $splitOutput = true)
    {
        $result = null;

        if (empty($this->executable)) {
            throw new \RuntimeException('You must add at least one command!');
        }

        if ($this->chained) {
            if (empty($this->stack)) {
                throw new \RuntimeException('This command cannot be chained!');
            }

            $process = new Process($this->getStacked());
            if ($dryrun) {
                return new Result(
                    0,
                    sprintf('Dryrun: %s would have been executed.', $process->getCommandLine())
                );
            }

            return $this->executeCommand($process, $splitOutput);
        }

        // Support non-chainable commands.
        if (empty($this->stack)) {
            $this->stack[] = [
                'exec' => $this->executable,
                'opts' => array_merge($this->options, $this->arguments),
            ];
        }

        foreach ($this->stack as $command) {
            // Create the CLI command to be executed.
            if ($raw === true) {
                // No escaping...
                $process = new Process($command['exec'] . ' ' . implode(' ', $command['opts']));
            } else {
                // The getProcess() call performs escaping on ALL options. This
                // might not be compatible with all commands!
                $builder = new ProcessBuilder($command['opts']);
                $process = $builder->setPrefix($command['exec'])
                    ->getProcess()
                ;
            }

            if ($dryrun) {
                $result = new Result(
                    0,
                    sprintf('Dryrun: %s would have been executed.', $process->getCommandLine())
                );
                continue;
            }

            $result = $this->executeCommand($process, $splitOutput);
            if (!$result->wasSuccessful()) {
                return $result;
            }
        }

        return $result;
    }

    /**
     * Use the Symfony Process class to actually execute the command.
     *
     * @param Process $process
     * @param bool $splitOutput Whether or not to split stdErr and stdOut in
     *             separate vars.
     *
     * @return Result
     */
    protected function executeCommand(Process $process, $splitOutput = true)
    {
        $process->setTimeout(null);

        if ($this->workingDirectory) {
            $process->setWorkingDirectory($this->workingDirectory);
        }

        $process->run();

        if ($splitOutput === true) {
            $output = 'StdOut:' . PHP_EOL;
            $output .= $process->getOutput();
            $output .= PHP_EOL . PHP_EOL . 'StdErr:' . PHP_EOL;
            $output .= $process->getErrorOutput();
        } else {
            $output = $process->getOutput();
        }

        return new Result($process->getExitCode(), $output);
    }

    /**
     * @see __toString()
     */
    public function generateScript()
    {
        return $this->__toString();
    }

    /**
     * @see __toString()
     */
    public function prettyPrint()
    {
        return $this->__toString();
    }

    /**
     * Print out the command stack as a usable bash script.
     *
     * @return string
     */
    public function __toString()
    {
        $script = self::SHEBANG . PHP_EOL . PHP_EOL;

        // If here to prevent adding excess whitespace.
        if ($opts = $this->getBashOptions(false, PHP_EOL)) {
            $script .= $opts . PHP_EOL;
        }

        foreach ($this->stack as $command) {
            $script .= $command->generateScript(true);
        }

        return $script;
    }
}
