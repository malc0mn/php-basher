<?php

namespace Basher\Tools\FileSystem\Zfs;

use Basher\Tools\OSBase;

/**
 * Sets the property or list of properties to the given value(s) for each
 * dataset. Only some properties can be edited. See the Properties section for
 * more information on what properties can be set and acceptable values.
 * Numeric values can be specified as exact values, or in a human-readable form
 * with a suffix of B, K, M, G, T, P, E, Z (for bytes, kilobytes, megabytes,
 * gigabytes, terabytes, petabytes, exabytes, or zettabytes, respectively).
 * User properties can be set on snapshots. For more information, see the User
 * Properties section.
 *
 * ``` php
 * <?php
 * ZFS::set('zpool1/myset')
 *   ->property('mountpoint', '/mount/myset')
 *   ->run()
 * ;
 * ?>
 * ```
 */
class ZfsSet extends OSBase
{
    /**
     * @var string
     */
    private $dataset;

    /**
     * Class constructor.
     *
     * @param string|null $dataset
     */
    public function __construct($dataset)
    {
        $this->executable('zfs');
        $this->option('set');

        // The dataset must be the last option!
        $this->dataset = $dataset;
    }

    public function property($name, $value)
    {
        $this->option($name, $value, '=');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run($dryrun = false, $raw = true, $splitOutput = true)
    {
        $this->option($this->dataset);

        return parent::run($dryrun, $raw, $splitOutput);
    }
}
