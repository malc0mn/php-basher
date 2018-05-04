<?php

namespace Basher\Tools\Database\Mysql;

/**
 * Mysql command line interface
 *
 * ```php
 * <?php
 * $mysql = Mysql::import('127.0.0.1');
 * $mysql->user('user', 'pass')
 *   ->database('dbname')
 *   ->import('/full/path/to/dump.sql')
 *   ->run()
 * ;
 * ?>
 * ```
 */
class Import extends Base
{
    /**
     * Class constructor.
     *
     * @param string $host
     */
    public function __construct($host)
    {
        $this->executable('mysql');

        parent::__construct($host);
    }

    /**
     * Import an SQL file.
     *
     * @param string $dumpfile
     *
     * @return self
     */
    public function import($dumpfile)
    {
        $this->argument("< $dumpfile");
        return $this;
    }
}
