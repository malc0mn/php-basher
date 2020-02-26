<?php

namespace Basher\Tools\FileSystem\Zfs;

use Basher\Tools\OSBase;

/**
 * Destroys the given dataset. By default, the command unshares any file systems
 * that are currently shared, unmounts any file systems that are currently
 * mounted, and refuses to destroy a dataset that has active dependents
 * (children or clones).
 *
 * ``` php
 * <?php
 * Zfs::destroy('zpool1/myset')
 *   ->recursive()
 *   ->run()
 * ;
 * ?>
 * ```
 */
class ZfsDestroy extends OSBase
{
    /**
     * Class constructor.
     *
     * @param string|null $dataset
     */
    public function __construct($dataset)
    {
        $this->executable('zfs');
        $this->option('destroy');

        $this->option($dataset);
    }

    /**
     * Recursively display any children of the dataset on the command line.
     *
     * @return self
     */
    public function recursive()
    {
        $this->option('-r');

        return $this;
    }
}
