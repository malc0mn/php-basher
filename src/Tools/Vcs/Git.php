<?php

/**
 * @file
 *
 * At some point in time, this was Based on the Robo PHP task runner.
 *
 * @see https://github.com/codegyre/robo/
 */

namespace Basher\Tools\Vcs;

use Basher\Tools\OSBase;

/**
 * Run Git commands. You can use chained() to indicate that all commands should
 * be concatenated by a double ampersand (&&).
 *
 * ```php
 * <?php
 * $git = new Git();
 * $git->clone('https://my.com/repo.git', '/opt/approot')
 *   ->checkout('master')
 *   ->run()
 * ;
 *
 * $git = new Git();
 * $git->add('-A')
 *   ->commit('adding everything')
 *   ->push('origin','master')
 *   ->tag('0.6.0')
 *   ->push('origin','0.6.0')
 *   ->run()
 * ;
 *
 * $git = new Git();
 * $git->add('doc/*')
 *   ->commit('doc updated')
 *   ->push()
 *   ->run()
 * ;
 * ?>
 * ```
 */
class Git extends OSBase
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->executable('git');
    }

    /**
     * Clone a repository into a new directory.
     *
     * @param string $repo repository url
     * @param string $to destination directory
     * @param string $branchOrTag branch or tag to clone
     * @param bool $shallow
     *
     * @return static
     */
    public function clone($repo, $to = '', $branchOrTag = '', $shallow = true)
    {
        $args = ['clone'];

        if ($shallow === true) {
            $args[] = '--depth 1';
        }

        if (!empty($branchOrTag)) {
            $args[] = "-b $branchOrTag";
        }

        $args[] = $repo;
        $args[] = $to;

        return $this->stack($args);
    }

    /**
     * Executes `git add` command with "files to add pattern".
     *
     * @param string $pattern
     *
     * @return self
     */
    public function add($pattern)
    {
        return $this->stack(['add', $pattern]);
    }

    /**
     * Executes 'git commit' command with a message.
     *
     * @param $message
     * @param string $options
     *
     * @return self
     */
    public function commit($message, $options = '')
    {
        return $this->stack(['commit', "-m '$message'", $options]);
    }

    /**
     * Executes 'git pull' command.
     *
     * @param string $origin
     *
     * @param string $branch
     *
     * @return self
     */
    public function pull($origin = '', $branch = '')
    {
        return $this->stack(['pull', $origin, $branch]);
    }

    /**
     * Executes 'git push' command.
     *
     * @param string $origin
     * @param string $branch
     *
     * @return self
     */
    public function push($origin = '', $branch = '')
    {
        return $this->stack(['push', $origin, $branch]);
    }

    /**
     * Performs 'git merge'.
     *
     * @param string $branch
     *
     * @return self
     */
    public function merge($branch)
    {
        return $this->stack(['merge', $branch]);
    }

    /**
     * Executes 'git checkout' command.
     *
     * @param $branch
     * @return self
     */
    public function checkout($branch)
    {
        return $this->stack(['checkout', $branch]);
    }

    /**
     * Executes 'git tag' command.
     *
     * @param $tagName
     * @param string $message
     *
     * @return self
     */
    public function tag($tagName, $message = '')
    {
        if ($message != '') {
            $message = "-m '$message'";
        }
        return $this->stack(['tag', $message, $tagName]);
    }
}
