<?php

namespace Basher\Tools\Lxc;

use Basher\CommandStack;

/**
 * Query information about a container.
 *
 * lxc-info queries and shows information about a container.
 *
 * ```php
 * <?php
 * Lxc::info('container-name')
 *   ->status()
 *   ->run()
 * ;
 * ?>
 * ```
 */
class Info extends CommandStack
{
    /**
     * Class constructor.
     *
     * @param string $containerName
     */
    public function __construct($containerName)
    {
        $this->executable('lxc-info');

        $this->option('-n', "'$containerName'");
    }

    /**
     * Just print the container's state.
     *
     * @return self
     */
    public function status()
    {
        $this->option('-s');
        return $this;
    }

    /**
     * Just print the container's pid.
     *
     * @return self
     */
    public function pid()
    {
        $this->option('-p');
        return $this;
    }

    /**
     * Just print the container's IP addresses.
     *
     * @return self
     */
    public function ip()
    {
        $this->option('-i');
        return $this;
    }

    /**
     * Just  print  the container's statistics.  Note that for performance
     * reasons the kernel does not account kernel memory use unless a kernel
     * memory limit is set. If a limit is not set, lxc-info will display kernel
     * memory use as 0. A limit can be set by specifying
     *
     *   lxc.cgroup.memory.kmem.limit_in_bytes = number
     *
     * in your container configuration file, see lxc.conf(5).
     *
     * @return self
     */
    public function stats()
    {
        $this->option('-S');
        return $this;
    }

    /**
     * Print the container's statistics in raw, non-humanized form. The default
     * is to print statistics in humanized form.
     *
     * @return self
     */
    public function statsRaw()
    {
        $this->option('-H');
        return $this;
    }
}
