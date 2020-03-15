<?php

/**
 * @file
 *
 * At some point in time, this was Based on the Robo PHP task runner.
 *
 * @see https://github.com/codegyre/robo/
 */

namespace Basher;

class Result
{
    const EXITCODE_OK = 0;

    /**
     * @var string
     */
    private $commandline;

    /**
     * @var int
     */
    private $exitCode;

    /**
     * @var string
     */
    private $output;

    /**
     * @var bool
     */
    private $dryrun;

    /**
     * Result constructor.
     *
     * @param string $commandline
     * @param int $exitCode
     * @param string $output
     * @param bool $dryrun
     */
    public function __construct($commandline, $exitCode, $output, $dryrun = false)
    {
        $this->commandline = $commandline;
        $this->exitCode = $exitCode;
        $this->output = $output;
        $this->dryrun = $dryrun;
    }

    /**
     * @return string
     */
    public function getCommandline()
    {
        return $this->commandline;
    }

    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return array
     */
    public function getOutputAsArray()
    {
        return explode("\n", $this->getOutput());
    }

    /**
     * @return bool
     */
    public function wasSuccessful()
    {
        return $this->exitCode === self::EXITCODE_OK;
    }

    /**
     * @return bool
     */
    public function wasDryrun()
    {
        return $this->dryrun;
    }
}
