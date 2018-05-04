<?php

namespace Basher\Tools\Lxc;

use Basher\CommandStack;

/**
 * Start a process inside a running container.
 *
 * lxc-attach runs the specified command inside the container specified by name.
 * The container has to be running already.
 *
 * If  no  command is specified, the current default shell of the user running
 * lxc-attach will be looked up inside the container and executed. This will
 * fail if no such user exists inside the container or the container does not
 * have a working nsswitch mechanism.
 *
 * ``` php
 * <?php
 * Lxc::attach('container-name')
 *   ->execute('ls -liah')
 *   ->run()
 * ;
 * ?>
 * ```
 */
class Attach extends CommandStack
{
    /**
     * @var string
     */
    private $container;


    /**
     * Class constructor.
     *
     * @param string $containerName
     * @param string $homeDir
     */
    public function __construct($containerName, $homeDir = "/root")
    {
        $this->executable('lxc-attach');
        // -v to set an additional variable: e.g. composer NEEDS a home dir to
        // be set in order to run.
        $this->container = ['-n', "'$containerName'", '-v', '"HOME=' . $homeDir . '"'];
    }

    /**
     * Execute (a) (the) given (set of) command(s) inside the container.
     *
     * @param string|array $commands
     * @param bool $wrapInBash
     *
     * @return self
     */
    public function execute($commands, $wrapInBash = true)
    {
        if (!is_array($commands)) {
            $commands = [$commands];
        }

        foreach ($commands as $command) {
            if ($wrapInBash === true) {
                // Escape single quotes in $command.
                $command = str_replace("'", "'\''", $command);
                $command = "bash -c '$command'";
            }
            $this->stack(array_merge($this->container, ['--', $command]));
        }

        return $this;
    }
}
