<?php

namespace Basher\Tools\Lxc;

use Basher\Tools\OSBase;

/**
 * Destroy a container.
 *
 * lxc-destroy destroys the system object previously created by the lxc-create
 * command.
 *
 * ```php
 * <?php
 * Lxc::destroy('container-name')
 *   ->force()
 *   ->run()
 * ;
 * ?>
 * ```
 */
class Destroy extends OSBase
{
    /**
     * Class constructor.
     *
     * @param string $containerName
     */
    public function __construct($containerName)
    {
        $this->executable('lxc-destroy');

        $this->option('-n', "'$containerName'");
    }

    /**
     * Simply perform the requestion action (reboot, shutdown, or hard kill) and
     * exit.
     *
     * @return self
     */
    public function force()
    {
        $this->option('-f');
        return $this;
    }
}
