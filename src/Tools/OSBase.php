<?php

namespace Basher\Tools;

use Basher\CommandStack;

/**
 * Contains the 'basic operating system' commands, like cd, rm, mv etc.
 */
class OSBase extends CommandStack
{
    const TYPE_DIR = 'dir';
    const TYPE_FILE = 'file';
    const TYPE_LINK = 'link';

    /**
     * Add a change dir command to the stack.
     *
     * @param string $dir
     *
     * @return static
     */
    public function changeDir($dir)
    {
        $this->stack($dir, null, 'cd');
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

        $this->stack($args, null, 'rm');

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

        $this->stack($args, $allowFail, 'ln');

        return $this;
    }

    /**
     * Create a directory.
     *
     * @param string $dir
     * @param bool $recursive
     *
     * @return static
     */
    public function makeDir($dir, $recursive = true)
    {
        $args = [];

        if ($recursive) {
            $args[] = '-p';
        }

        $args[] = $dir;

        $this->stack($args, null, 'mkdir');

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

        $this->stack($args, $allowFail, 'mv');

        return $this;
    }

    /**
     * Add a rename/move if exists command to the stack.
     *
     * @param string $source
     * @param string $dest
     * @param bool $force
     * @param array $types
     *
     * @return static
     *
     * @throws \RuntimeException
     */
    public function moveIfExists(
        $source,
        $dest,
        $force = true,
        array $types = [self::TYPE_DIR, self::TYPE_FILE, self::TYPE_LINK]
    ) {
        // Set arguments.
        if ($force === true) {
            $args[] = '-f';
        }
        $args[] = $source;
        $args[] = $dest;
        $args[] = '; fi';

        $check = [];
        foreach ($types as $type) {
            switch ($type) {
                case self::TYPE_DIR:
                    $check[] = "-d $source";
                    break;
                case self::TYPE_FILE:
                    $check[] = "-f $source";
                    break;
                case self::TYPE_LINK:
                    $check[] = "-L $source";
                    break;
                default:
                    throw new \RuntimeException(sprintf('Unknown filetype %s!', $type));
            }
        }
        $check = implode(' -o ', $check);

        $this->stack($args, null, "if [ $check ]; then mv");

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
     * @param array $types
     *
     * @return static
     */
    public function renameIfExists(
        $source,
        $dest,
        $force = true,
        array $types = [self::TYPE_DIR,self::TYPE_FILE, self::TYPE_LINK]
    ) {
        return $this->moveIfExists($source, $dest, $force, $types);
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

        $this->stack($args, $allowFail, 'setfacl');

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
            $action,
        ];

        $this->stack($args, $allowFail, 'service');

        return $this;
    }
}
