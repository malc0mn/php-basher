<?php

namespace Basher;

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
     * @param string|null $join
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
