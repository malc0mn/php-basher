<?php

namespace Basher\Tools;

/**
 * The basic command stack always concerns a SINGLE executable. This class
 * allows you to easily create 'oneliners' of concerning DIFFERENT executables.
 */
class OneLiner extends OSBase
{
    /**
     * Push a command to the command execution stack.
     *
     * @param string $executable
     * @param string|array $options
     * @param bool $allowFail when the command will be executed as 'chained',
     *                        this particular part of the stack will be added to
     *                        the chain using a trailing semi-colon (;) so that
     *                        any of the following commands will be executed
     *                        regardless of the outcome.
     * @param array $envVars An associative array with the variable name as key
     *                       and the value as, well, value.
     *
     * @return static
     */
    public function addCmd(
        $executable,
        $options,
        $allowFail = false,
        array $envVars = []
    ) {
        return $this->stack($options, $allowFail, $executable, $envVars);
    }

    /**
     * Prepend a command to the command execution stack.
     *
     * @param string $executable
     * @param string|array $options
     * @param bool $allowFail when the command will be executed as 'chained',
     *                        this particular part of the stack will be added to
     *                        the chain using a trailing semi-colon (;) so that
     *                        any of the following commands will be executed
     *                        regardless of the outcome.
     * @param array $envVars An associative array with the variable name as key
     *                       and the value as, well, value.
     *
     * @return static
     */
    public function prependCmd(
        $executable,
        $options,
        $allowFail = false,
        array $envVars = []
    ) {
        $this->addCmd($executable, $options, $allowFail, $envVars);
        $this->stack = array_merge(array_splice($this->stack, -1), $this->stack);
        return $this;
    }
}
