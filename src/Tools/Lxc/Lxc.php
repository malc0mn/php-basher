<?php

namespace Basher\Tools\Lxc;

/**
 * PHP abstraction of the LXC commands toolset.
 *
 * @package Basher\Tools\Lxc
 */
class Lxc
{
    /**
     * Call lxc-start.
     *
     * @param string $containerName
     *
     * @return Start
     */
    public static function start($containerName)
    {
        return new Start($containerName);
    }

    /**
     * Call lxc-attach.
     *
     * @param string $containerName
     *
     * @return Attach
     */
    public static function attach($containerName)
    {
        return new Attach($containerName);
    }

    /**
     * Call lxc-stop.
     *
     * @param string $containerName
     *
     * @return Stop
     */
    public static function stop($containerName)
    {
        return new Stop($containerName);
    }

    /**
     * Call lxc-info.
     *
     * @param string $containerName
     *
     * @return Info
     */
    public static function info($containerName)
    {
        return new Info($containerName);
    }

    /**
     * Call lxc-destroy.
     *
     * @param string $containerName
     *
     * @return Destroy
     */
    public static function destroy($containerName)
    {
        return new Destroy($containerName);
    }
}
