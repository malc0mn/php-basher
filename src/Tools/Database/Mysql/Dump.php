<?php

namespace Basher\Tools\Database\Mysql;

/**
 * Mysql command line interface
 *
 * ```php
 * <?php
 * $mysql = Mysql::dump('127.0.0.1');
 * $mysql->user('user', 'pass')
 *   ->database('dbname')
 *   ->output('/full/path/to/dump.sql')
 *   ->run()
 * ;
 * ?>
 * ```
 */
class Dump extends Base
{
    /**
     * Class constructor.
     *
     * @param string $host
     */
    public function __construct($host)
    {
        $this->executable('mysqldump');

        parent::__construct($host);
    }

    /**
     * Perform single transaction dump.
     *
     * @return self
     */
    public function singleTransaction()
    {
        $this->option('--single-transaction');

        return $this;
    }

    /**
     * Write dump to an SQL file.
     *
     * @param string $dumpfile
     *
     * @return self
     */
    public function output($dumpfile)
    {
        $this->argument("> $dumpfile");

        return $this;
    }
}
