<?php

namespace Basher\Tools\FileSystem\Zfs;

use Basher\Tools\OSBase;

/**
 * Lists the given pools along with a health status and space usage. If no pools
 * are specified, all pools in the system are listed.  When given an interval,
 * the information is printed every interval seconds until ^C is pressed.
 * If count is specified, the command exits after count reports are printed.
 *
 * ``` php
 * <?php
 * Zpool::list()
 *   ->noHeader()
 *   ->fields(['name', 'cap'])
 *   ->sizeInBytes()
 *   ->run()
 * ;
 * ?>
 * ```
 */
class ZpoolList extends OSBase
{
    /**
     * Class constructor.
     *
     * @param string|null $pool
     */
    public function __construct($pool = null)
    {
        $this->executable('zpool');
        $this->option('list');

        if (!empty($pool)) {
            $this->option($pool);
        }
    }

    /**
     * Used for scripting mode. Do not print headers and separate fields by a
     * single tab instead of arbitrary white space.
     *
     * @return self
     */
    public function noHeader()
    {
        $this->option('-H');

        return $this;
    }

    /**
     * Display numbers in parsable (exact) values.
     *
     * @return self
     */
    public function sizeInBytes()
    {
        $this->option('-p');

        return $this;
    }

    /**
     * Comma-separated list of properties to display.  See the Properties
     * section for a list of valid properties.  The default list is name, size,
     * alloc, free, fragmentation, expandsize, capacity, dedupratio, health,
     * altroot.
     *
     * @param string|array
     *
     * @return self
     */
    public function fields($fields)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        $this->option('-o');
        $this->option(implode(',', $fields));

        return $this;
    }
}
