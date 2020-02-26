<?php

namespace Basher\Tools\FileSystem\Zfs;

use Basher\Tools\OSBase;

/**
 * Mount ZFS filesystem on a path described by its mountpoint property, if the
 * path exists and is empty. If mountpoint is set to legacy, the filesystem
 * should be instead mounted using mount(8).
 *
 * ``` php
 * <?php
 * ZFS::mount('zpool1/myset')
 *   ->all()
 *   ->run()
 * ;
 * ?>
 * ```
 */
class ZfsMount extends OSBase
{
    /**
     * Class constructor.
     *
     * @param string|null $dataset
     */
    public function __construct($dataset)
    {
        $this->executable('zfs');
        $this->option('mount');

        $this->option($dataset);
    }

    /**
     * Perform an overlay mount. Allows mounting in non-empty mountpoint. See
     * mount(8) for more information.
     *
     * @return self
     */
    public function overlay()
    {
        $this->option('-O');

        return $this;
    }

    /**
     * Mount all available ZFS file systems. Invoked automatically as part of
     * the boot process if configured.
     *
     * @return self
     */
    public function all()
    {
        $this->option('-a');

        return $this;
    }

    /**
     * An optional, comma-separated list of mount options to use temporarily for
     * the duration of the mount. See the Temporary Mount Point Properties
     * section for details.
     *
     * @return self
     */
    public function options($options)
    {
        if (!is_array($options)) {
            $options = [$options];
        }

        $this->option('-o');
        $this->option(implode(',', $options));

        return $this;
    }

    /**
     * Load keys for encrypted filesystems as they are being mounted. This is
     * equivalent to executing zfs load-key on each encryption root before
     * mounting it. Note that if a filesystem has a keylocation of prompt this
     * will cause the terminal to interactively block after asking for the key.
     *
     * @return self
     */
    public function loadKeys()
    {
        $this->option('-l');

        return $this;
    }

    /**
     * Report mount progress.
     *
     * @return self
     */
    public function verbose()
    {
        $this->option('-v');

        return $this;
    }
}
