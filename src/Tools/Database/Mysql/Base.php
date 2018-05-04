<?php

namespace Basher\Tools\Database\Mysql;

use Basher\CommandStack;

abstract class Base extends CommandStack
{
    /**
     * Class constructor.
     *
     * @param string $host
     */
    public function __construct($host)
    {
        $this->option('-h', $host);
    }

    /**
     * Set user and password.
     *
     * @param string $user
     * @param string $password
     *
     * @return self
     */
    public function user($user, $password = null)
    {
        $this->option('-u', $user);

        if ($password !== null) {
            $this->option('-p', $password, '');
        }

        return $this;
    }

    /**
     * Set the database to perform the actions on.
     *
     * @param string $dbname
     *
     * @return self
     */
    public function database($dbname)
    {
        $this->argument($dbname);
        return $this;
    }
}
