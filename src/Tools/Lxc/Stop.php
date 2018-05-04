<?php

namespace Basher\Tools\Lxc;

use Basher\CommandStack;

/**
 * Stop the application running inside a container.
 *
 * lxc-stop reboots, cleanly shuts down, or kills all the processes inside the
 * container. By default, it will request a clean shutdown of the container by
 * sending lxc.haltsignal (defaults to SIGPWR) to the container's init process,
 * waiting up to 60 seconds for the container to exit, and then returning. If
 * the container fails to cleanly exit in 60 seconds, it will be sent the
 * lxc.stopsignal (defaults to SIGKILL) to force it to shut down.
 *
 * The [-W], [-r], [-k] and [--nokill] options specify the action to perform.
 * [-W] indicates that after performing the specified action, lxc-stop should
 * immediately exit, while [-t TIMEOUT] specifies the maximum amount of time to
 * wait for the container to complete the shutdown or reboot.
 *
 * ```php
 * <?php
 * Lxc::stop('container-name')
 *   ->reboot()
 *   ->noWait()
 *   ->run()
 * ;
 * ?>
 * ```
 */
class Stop extends CommandStack
{
    /**
     * Class constructor.
     *
     * @param string $containerName
     */
    public function __construct($containerName)
    {
        $this->executable('lxc-stop');

        $this->option('-n', "'$containerName'");
    }

    /**
     * Simply perform the requestion action (reboot, shutdown, or hard kill) and
     * exit.
     *
     * @return self
     */
    public function noWait()
    {
        $this->option('-W');
        return $this;
    }

    /**
     * Request a reboot of the container.
     *
     * @return self
     */
    public function reboot()
    {
        $this->option('-r');
        return $this;
    }

    /**
     * Wait TIMEOUT seconds before hard-stopping the container.
     *
     * @param int $seconds
     *
     * @return self
     */
    public function timeout($seconds)
    {
        $this->option('-t', $seconds);
        return $this;
    }

    /**
     * Rather than requesting a clean shutdown of the container, explicitly kill
     * all tasks in the container. This is the legacy lxc-stop behavior.
     *
     * @return self
     */
    public function kill()
    {
        $this->option('-k');
        return $this;
    }

    /**
     * Only request a clean shutdown, do not kill the container tasks if the
     * clean shutdown fails.
     *
     * @return self
     */
    public function noKill()
    {
        $this->option('--nokill');
        return $this;
    }

    /**
     * This option avoids the use of any of the API lxc locking, and should only
     * be used if lxc-stop is hanging due to a bad system state.
     *
     * @return self
     */
    public function noLock()
    {
        $this->option('--nolock');
        return $this;
    }
}
