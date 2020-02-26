<?php

namespace Basher\Tools\FileSystem\Zfs;

use Basher\Tools\OSBase;

/**
 * Lists the property information for the given datasets in tabular form. If
 * specified, you can list property information by the absolute pathname or the
 * relative pathname. By default, all file systems and volumes are displayed.
 * Snapshots are displayed if the listsnaps property is on (the default is off).
 * The following fields are displayed: name, used, available, referenced,
 * mountpoint.
 *
 * ``` php
 * <?php
 * Zfs::list()
 *   ->noHeader()
 *   ->fields(['name', 'cap'])
 *   ->run()
 * ;
 * ?>
 * ```
 */
class ZfsList extends OSBase
{
    /**
     * Class constructor.
     *
     * @param string|null $dataset
     */
    public function __construct($dataset = null)
    {
        $this->executable('zfs');
        $this->option('list');

        if (!empty($dataset)) {
            $this->option($dataset);
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
     * Recursively display any children of the dataset on the command line.
     *
     * @return self
     */
    public function recursive()
    {
        $this->option('-r');

        return $this;
    }

    /**
     * A comma-separated list of properties to display. The property must be:
     *  - One of the properties described in the Native Properties section
     *  - A user property
     *  - The value name to display the dataset name
     *  - The value space to display space usage properties on file systems
     *    and volumes. This is a shortcut for specifying
     *    -o name,avail,used,usedsnap,usedds,usedrefreserv,usedchild
     *    -t filesystem,volume syntax.
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
