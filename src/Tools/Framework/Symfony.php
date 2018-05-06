<?php

namespace Basher\Tools\Framework;

use Basher\Tools\OSBase;

/**
 * Composer: the PHP package manager.
 *
 * ```php
 * <?php
 * $symfony = new Symfony();
 * $symfony->cacheClear('dev')
 *   ->assetsInstall()
 *   ->run()
 * ;
 * ?>
 * ```
 */
class Symfony extends OSBase
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->executable('php bin/console');
    }

    /**
     * Clears the cache.
     *
     * @param string $env
     * @param bool $noDebug
     *
     * @return self
     */
    public function cacheClear($env = 'prod', $noDebug = true)
    {
        $args = ['cache:clear', "--env=$env"];

        if ($noDebug === true) {
            $args[] = '--no-debug';
        }

        $this->stack($args);
        return $this;
    }

    /**
     * Installs bundles web assets under a public web directory.
     *
     * @param string $dir
     * @param bool $symlink
     * @param bool $relative
     *
     * @return self
     */
    public function assetsInstall($dir = 'web', $symlink = true, $relative = true)
    {
        $args = ['assets:install', $dir];

        if ($symlink === true) {
            $args[] = '--symlink';
        }

        if ($relative === true) {
            $args[] = '--relative';
        }

        $this->stack($args);
        return $this;
    }

    /**
     * Dumps all assets to the filesystem.
     *
     * @param string $env
     * @param bool $noDebug
     *
     * @return self
     */
    public function asseticDump($env = 'prod', $noDebug = true)
    {
        $args = ['assetic:dump', "--env=$env"];

        if ($noDebug === true) {
            $args[] = '--no-debug';
        }

        $this->stack($args);
        return $this;
    }

    /**
     * Set ACL for var directory with setfacl
     *
     * @param string $varDirectory
     *
     * @return self
     */
    public function varSetFacl($varDirectory)
    {
        $users = ['www-data', 'root'];

        foreach($users as $user) {
            // Set acl on existing files
            $this->setFacl($varDirectory, $user, 'rwX', true, false, false);

            // Set as default acl for new files
            $this->setFacl($varDirectory, $user, 'rwX', true, true, false);
        }

        return $this;
    }
}
