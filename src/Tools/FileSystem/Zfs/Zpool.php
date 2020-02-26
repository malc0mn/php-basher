<?php

namespace Basher\Tools\FileSystem\Zfs;

/**
 * PHP abstraction of the ZFS zpool commands toolset.
 *
 * @package Basher\Tools\FileSystem\Zfs
 */
class Zpool
{
    /**
     * Call zpool list.
     *
     * @param string|null $dataset
     *
     * @return ZpoolList
     */
    public static function list($dataset = null)
    {
        return new ZpoolList($dataset);
    }
}
