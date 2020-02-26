<?php

namespace Basher\Tools\FileSystem\Zfs;

use Basher\Tools\OSBase;

/**
 * Unmounts currently mounted ZFS file systems.
 * ``` php
 * <?php
 * ZFS::unmount('zpool1/myset')
 *   ->run()
 * ;
 * ?>
 * ```
 */
class ZfsUnmount extends OSBase
{
    /**
     * Class constructor.
     *
     * @param string|null $dataset
     */
    public function __construct($dataset)
    {
        $this->executable('zfs');
        $this->option('unmount');

        $this->option($dataset);
    }

    /**
     * Unmount all available ZFS file systems. Invoked automatically as part of
     * the shutdown process.
     *
     * @return self
     */
    public function all()
    {
        $this->option('-a');

        return $this;
    }

    /**
     * Forcefully unmount the file system, even if it is currently in use.
     *
     * @return self
     */
    public function force()
    {
        $this->option('-f');

        return $this;
    }
}
