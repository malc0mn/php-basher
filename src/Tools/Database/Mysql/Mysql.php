<?php

namespace Basher\Tools\Database\Mysql;

/**
 * PHP abstraction of the MySQL commands.
 *
 * @package Basher\Tools\Mysql
 */
class Mysql
{
    /**
     * Call mysql.
     *
     * @param string $host
     *
     * @return Create
     */
    public static function create($host)
    {
        return new Create($host);
    }

    /**
     * Call mysql.
     *
     * @param string $host
     *
     * @return Import
     */
    public static function import($host)
    {
        return new Import($host);
    }

    /**
     * Call mysqldump.
     *
     * @param string $host
     *
     * @return Dump
     */
    public static function dump($host)
    {
        return new Dump($host);
    }
}
