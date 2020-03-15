<?php

namespace Basher;

use Symfony\Component\Process\Process;

class Command
{
    /**
     * @var string
     */
    private $executable;

    /**
     * @var array
     */
    private $options;

    /**
     * @var bool
     */
    private $allowFail;

    /**
     * @var array
     */
    private $envVars = [];

    /**
     * Command constructor.
     *
     * @param string $executable
     * @param string| array $options
     * @param array $envVars
     * @param string|null $allowFail
     */
    public function __construct(
        $executable,
        $options,
        array $envVars = [],
        $allowFail = null
    ) {
        $this->executable = $executable;

        if (!is_array($options)) {
            $options = [$options];
        }
        // Removes all NULL, FALSE and 'empty strings' but leaves 0 (zero)
        // values.
        $this->options = array_filter($options, 'strlen');
        $this->envVars = $envVars;
        $this->allowFail = $allowFail;
    }

    /**
     * Get command line 'join' based on whether or not the command is allowed to
     * fail
     *
     * @return string
     */
    public function getJoin()
    {
        $join = ' && ';
        if ($this->allowFail === true) {
            $join = '; ';
        }

        return $join;
    }

    /**
     * Convert to executable command for use in a script.
     *
     * @return string
     */
    public function generateScript($newLine = false)
    {
        return $this->renderEnvVars() .
            $this->executable .
            ' ' .
            implode(' ', $this->options) .
            ($newLine ? PHP_EOL : '')
        ;
    }

    /**
     * Convert the command to a Symfony Process class.
     *
     * @param bool $escape
     *
     * @return Process
     */
    public function toProcess($escape = true)
    {
        // Create the CLI command to be executed.
        if (false === $escape) {
            // No escaping...
            $commandline = $this->generateScript();
        } else {
            // Passing an array to the Process constructor performs escaping
            // on ALL options. This might not be compatible with all commands!
            $commandline = array_merge([$this->executable], $this->options);
        }

        $process = new Process($commandline);

        if ($this->envVars) {
            $process->setEnv($this->envVars);
        }

        return $process;
    }

    /**
     * Render env vars for use in a script.
     *
     * @return string
     */
    protected function renderEnvVars()
    {
        $rendered = '';

        // This simple loop was faster than any array_*()/implode() combo!
        foreach ($this->envVars as $name => $value) {
            // Leave the trailing space here!!
            $rendered .= "$name=$value ";
        }

        return $rendered;
    }
}
