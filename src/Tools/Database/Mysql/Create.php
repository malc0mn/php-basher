<?php

namespace Basher\Tools\Database\Mysql;

/**
 * Mysql command line interface
 *
 * ```php
 * <?php
 * $mysql = Mysql::create('127.0.0.1');
 * $mysql->addDatabase('dbname')
 *   ->addUser('user', 'pass')
 *   ->prepare()
 *   ->run($output)
 * ;
 * ?>
 * ```
 */
class Create extends Base
{
    /**
     * @var string
     */
    private $sql = '';

    /**
     * @var string
     */
    private $dbname = '';

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
     * Set the database without creating it.
     *
     * @param $dbname
     *
     * @return self
     */
    public function setDatabase($dbname)
    {
        $this->dbname = $dbname;

        return $this;
    }

    /**
     * Create a new database.
     *
     * @param string $dbname
     *
     * @return self
     */
    public function addDatabase($dbname)
    {
        $this->dbname = $this->sanitize($dbname);
        $this->sql .= sprintf('CREATE DATABASE `%s`;', $this->dbname);

        return $this;
    }

    /**
     * Create a new user.
     *
     * @param string $user
     * @param string $password
     * @param bool|string $super Whether or not the user requires super privileges.
     *
     * @return self
     *
     * @throws \Exception
     */
    public function addUser($user, $password, $super = false)
    {
        if (empty($this->dbname)) {
            throw new \Exception('Please create a database first using the addDatabase() method or set the database using the setDatabase() method!');
        }

        $user = $this->sanitize($user);
        // Escape the double quote " by doubling it up.
        $password = str_replace('"', '""', $password);

        // Allow connect from any IP.
        $this->sql .= sprintf('GRANT ALL PRIVILEGES ON `%s`.* TO "%s"@"%%" IDENTIFIED BY "%s";', $this->dbname, $user, $password);
        // Allow connect from localhost.
        $this->sql .= sprintf('GRANT ALL PRIVILEGES ON `%s`.* TO "%s"@"localhost" IDENTIFIED BY "%s";', $this->dbname, $user, $password);
        if ($super === true) {
            $this->sql .= sprintf('GRANT SUPER ON *.* TO "%s"@"%%";', $user);
            $this->sql .= sprintf('GRANT SUPER ON *.* TO "%s"@"localhost";', $user);
        }
        $this->sql .= "FLUSH PRIVILEGES;";

        return $this;
    }

    /**
     * Create a new admin user.
     *
     * @param string $user
     * @param string $password
     *
     * @return self
     *
     * @throws \Exception
     */
    public function addAdminUser($user, $password)
    {
        $user = $this->sanitize($user);
        // Escape the double quote " by doubling it up.
        $password = str_replace('"', '""', $password);

        // Allow connect from any IP.
        $this->sql .= sprintf('GRANT ALL PRIVILEGES ON *.* TO "%s"@"%%" IDENTIFIED BY "%s" WITH GRANT OPTION;', $user, $password);
        // Allow connect from localhost.
        $this->sql .= sprintf('GRANT ALL PRIVILEGES ON *.* TO "%s"@"localhost" IDENTIFIED BY "%s" WITH GRANT OPTION;', $user, $password);
        $this->sql .= "FLUSH PRIVILEGES;";

        return $this;
    }

    /**
     * Prepare the statement for execution.
     *
     * @param bool $escape
     *
     * @return $this
     */
    public function prepare($escape = true)
    {
        // Escape single quotes when used, e.g. with bash -c '[command here]';
        if ($escape === true) {
            $sql = str_replace("'", "'\''", $this->sql);
            $args = "-e '" . $sql . "'";
        } else {
            $args = "-e '" . $this->sql . "'";
        }
        $this->stack($args);

        return $this;
    }

    /**
     * Make sure the string is safe for use.
     *
     * @param $string
     *
     * @return string
     */
    private function sanitize($string)
    {
        return preg_replace('/[^0-9a-zA-Z_]+/', '', $string);
    }
}
