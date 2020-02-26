<?php

namespace Basher\Tools\FileSystem\Zfs;

/**
 * PHP abstraction of the ZFS commands toolset.
 *
 * @package Basher\Tools\FileSystem\Zfs
 */
class Zfs
{
    /**
     * Call zfs list.
     *
     * @param string|null $dataset
     *
     * @return ZfsDestroy
     */
    public static function destroy($dataset)
    {
        return new ZfsDestroy($dataset);
    }

    /**
     * Call zfs list.
     *
     * @param string|null $dataset
     *
     * @return ZfsList
     */
    public static function list($dataset = null)
    {
        return new ZfsList($dataset);
    }

    /**
     * Call zfs set.
     *
     * @param string $dataset
     *
     * @return ZfsSet
     */
    public static function set($dataset)
    {
        return new ZfsSet($dataset);
    }

    /**
     * Call zfs set.
     *
     * @param string $dataset
     *
     * @return ZfsUnmount
     */
    public static function unmount($dataset)
    {
        return new ZfsUnmount($dataset);
    }
}
