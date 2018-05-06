<?php

namespace Basher\Tools\PackageManager;

use Basher\Tools\OSBase;

/**
 * Composer: the PHP package manager.
 *
 * ```php
 * <?php
 * $composer = new Composer();
 * $composer->install()
 *   ->run()
 * ;
 * ?>
 * ```
 */
class Composer extends OSBase
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->executable('composer');
    }

    /**
     * Installs the project dependencies from the composer.lock file if present,
     * or falls back on the composer.json.
     *
     * @param bool $noInteraction
     *
     * @return self
     */
    public function install($noInteraction = true)
    {
        $args = ['install'];
        if ($noInteraction === true) {
            $args[] = '-n';
        }
        $this->stack($args);
        return $this;
    }

    /**
     * Clears composer's internal package cache.
     *
     * @return self
     */
    public function clearCache()
    {
        $this->stack('clear-cache');
        return $this;
    }

    /**
     * Diagnoses the system to identify common errors.
     *
     * @return self
     */
    public function diagnose()
    {
        $this->stack('diagnose');
        return $this;
    }
}
