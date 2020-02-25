<?php

namespace Basher\Tools\FileSystem\Zfs;

use Basher\Tools\OSBase;

class Zfs extends OSBase
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->executable('zfs');
    }

    /**
     * List one or more zfs pools.
     *
     * @param string $zpool
     * @param array|string $fields
     * @param bool $noHeader
     * @param bool $sizeInBytes
     *
     * @return self
     */
    public function list($zpool = null, $fields = [], $noHeader = true, $sizeInBytes = true)
    {
        $args = ['list'];

        if (!empty($zpool)) {
            $args[] = $zpool;
        }

        // Used for scripting mode.  Do not print headers and separate fields by
        // a single tab instead of arbitrary white space.
        if ($noHeader) {
            $args[] = '-H';
        }

        // Display numbers in parsable (exact) values.
        if ($sizeInBytes) {
            $args[] = '-p';
        }

        // A comma-separated list of properties to display. The property must be:
        //  - One of the properties described in the Native Properties section
        //  - A user property
        //  - The value name to display the dataset name
        //  - The value space to display space usage properties on file systems
        //    and volumes. This is a shortcut for specifying
        //    -o name,avail,used,usedsnap,usedds,usedrefreserv,usedchild
        //    -t filesystem,volume syntax.
        if (!empty($fields)) {
            if (!is_array($fields)) {
                $fields = [$fields];
            }
            $args[] = '-o';
            $args[] = implode(',', $fields);
        }

        return $this->stack($args);
    }
}
