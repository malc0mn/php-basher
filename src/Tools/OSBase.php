<?php

namespace Basher\Tools;

use Basher\CommandStack;

/**
 * Contains the 'basic operating system' commands, like cd, rm, mv etc.
 */
class OSBase extends CommandStack
{

    /**
     * Add a change dir command to the stack.
     *
     * @param string $dir
     *
     * @return static
     */
    public function changeDir($dir)
    {
        $this->stack[] = [
            'exec' => 'cd',
            'opts' => [$dir],
        ];
        return $this;
    }

    /**
     * Delete file. Remember: -f won't complain about non existing files!
     *
     * @param $dirOrFile
     * @param bool $force
     * @param bool $recursive
     *
     * @return static
     */
    public function delete($dirOrFile, $force = true, $recursive = false)
    {
        // Set arguments.
        if ($force === true) {
            $args[] = '-f';
        }
        // Set arguments.
        if ($recursive === true) {
            $args[] = '-r';
        }
        $args[] = $dirOrFile;

        $this->stack[] = [
            'exec' => 'rm',
            'opts' => $args,
        ];

        return $this;
    }

    /**
     * Add a rename/move command to the stack.
     *
     * @param string $source
     * @param string $dest
     * @param bool $symbolic
     * @param bool $allowFail
     *
     * @return static
     */
    public function link($source, $dest, $symbolic = true, $allowFail = false)
    {
        $args = [];

        if ($symbolic === true) {
            $args[] = '-s';
        }

        $args[] = $source;
        $args[] = $dest;

        $this->stack[] = [
            'exec' => 'ln',
            'opts' => $args,
            'join' => $this->allowFail($allowFail),
        ];
        return $this;
    }

    /**
     * Add a rename or move command to the stack.
     *
     * @param string $source
     * @param string $dest
     * @param bool $force
     * @param bool $allowFail
     *
     * @return static
     */
    public function move($source, $dest, $force = true, $allowFail = false)
    {
        // Set arguments.
        if ($force === true) {
            $args[] = '-f';
        }
        $args[] = $source;
        $args[] = $dest;

        // Add to stack.
        $this->stack[] = [
            'exec' => 'mv',
            'opts' => $args,
            'join' => $this->allowFail($allowFail),
        ];
        return $this;
    }

    /**
     * Add a rename/move if exists command to the stack.
     *
     * @param string $source
     * @param string $dest
     * @param bool $force
     *
     * @return static
     */
    public function moveIfExists($source, $dest, $force = true)
    {
        // Set arguments.
        if ($force === true) {
            $args[] = '-f';
        }
        $args[] = $source;
        $args[] = $dest;
        $args[] = '; fi';

        // Add to stack.
        $this->stack[] = [
            'exec' => "if [ -d $source -o -L $source -o -f $source ]; then mv",
            'opts' => $args,
        ];
        return $this;
    }

    /**
     * An alias for move().
     *
     * @see move()
     *
     * @param string $source
     * @param string $dest
     * @param bool $force
     * @param bool $allowFail
     *
     * @return static
     */
    public function rename($source, $dest, $force = true, $allowFail = false)
    {
        return $this->move($source, $dest, $force, $allowFail);
    }

    /**
     * An alias for moveIfExists().
     *
     * @see moveIfExists()
     *
     * @param string $source
     * @param string $dest
     * @param bool $force
     *
     * @return static
     */
    public function renameIfExists($source, $dest, $force = true)
    {
        return $this->moveIfExists($source, $dest, $force);
    }

    /**
     * Add access controll lists to files and/or directories.
     *
     * @param string $destination
     * @param string $user
     * @param string $permissions
     * @param bool $recursive
     * @param bool $default
     * @param bool $allowFail
     *
     * @return static
     */
    public function setFacl(
        $destination,
        $user,
        $permissions = 'rwX',
        $recursive = true,
        $default = false,
        $allowFail = false
    ) {
        $args = [];

        if ($recursive === true) {
            $args[] = '-R';
        }
        if ($default === true) {
            $args[] = '-d';
        }

        $args[] = "-m u:\"$user\":$permissions";
        $args[] = $destination;

        $this->stack[] = [
            'exec' => 'setfacl',
            'opts' => $args,
            'join' => $this->allowFail($allowFail),
        ];

        return $this;
    }

    /**
     * Perform an action on a given service.
     *
     * @param string $name
     * @param string $action
     * @param bool $allowFail
     *
     * @return static
     */
    public function service(
        $name,
        $action = 'reload',
        $allowFail = false
    ) {
        $args = [
            $name,
            $action
        ];

        $this->stack[] = [
            'exec' => 'service',
            'opts' => $args,
            'join' => $this->allowFail($allowFail),
        ];

        return $this;
    }
}
