<?php

namespace Basher\Tools\Lxc;

use Basher\Tools\OSBase;

/**
 * Run an application inside a container.
 *
 * lxc-start runs the specified command inside the container specified by name.
 *
 * It  will setup the container according to the configuration previously
 * defined with the lxc-create command or with the configuration file parameter.
 * If no configuration is defined, the default isolation is used.
 *
 * If no command is specified, lxc-start will use the default "/sbin/init"
 * command to run a system container.
 *
 * ```php
 * <?php
 * Lxc::start('container-name')
 *   ->daemon()
 *   ->run()
 * ;
 * ?>
 * ```
 */
class Start extends OSBase
{
    /**
     * Class constructor.
     *
     * @param string $containerName
     */
    public function __construct($containerName)
    {
        $this->executable('lxc-start');

        $this->option('-n', "'$containerName'");
    }

    /**
     * Run the container as a daemon. As the container has no more tty, if an
     * error occurs nothing will be displayed, the log file can be used to check
     * the error.
     *
     * @return self
     */
    public function daemon()
    {
        $this->option('-d');
        return $this;
    }

    /**
     * Run the container in the foreground. In this mode, the container console
     * will be attached to the current tty and signals will be routed directly
     * to the container. (This  is  the  default mode.)
     *
     * @return self
     */
    public function foreground()
    {
        $this->option('-F');
        return $this;
    }

    /**
     * Create a file with the process id.
     *
     * @param string $pidFile
     *
     * @return self
     */
    public function pidFile($pidFile)
    {
        $this->option('-p', $pidFile);
        return $this;
    }

    /**
     * Specify the configuration file to configure the virtualization and
     * isolation functionalities for the container.
     * This configuration file if present will be used even if there is already
     * a configuration file present in the previously created container
     * (via lxc-create).
     *
     * @param string $configFile
     *
     * @return self
     */
    public function rcFile($configFile)
    {
        $this->option('-f', $configFile);
        return $this;
    }

    /**
     * Specify a device to use for the container's console, for example
     * /dev/tty8. If this option is not specified the current terminal will be
     * used unless -d is specified.
     *
     * @param string $consoleDevice
     *
     * @return self
     */
    public function console($consoleDevice)
    {
        $this->option('-c', $consoleDevice);
        return $this;
    }

    /**
     * Specify a file to log the container's console output to.
     *
     * @param string $consoleLogfile
     *
     * @return self
     */
    public function consoleLog($consoleLogfile)
    {
        $this->option('-L', $consoleLogfile);
        return $this;
    }

    /**
     * Assign value VAL to configuration variable KEY. This overrides any
     * assignment done in config_file.
     *
     * @param string $key
     * @param string $value
     *
     * @return self
     */
    public function define($key, $value)
    {
        $this->option('-s', "$key=$value");
        return $this;
    }

    /**
     * If any file descriptors are inherited, close them. If this option is not
     * specified, then lxc-start will exit with failure instead. Note: --daemon
     * implies --close-all-fds.
     *
     * @return self
     */
    public function closeAllFds()
    {
        $this->option('-C');
        return $this;
    }

    /**
     * Inherit a network namespace from a name container or a pid. The network
     * namespace will continue to be managed by the original owner. The network
     * configuration of the starting container is ignored and the up/down
     * scripts won't be executed.
     *
     * @param string|int $nameOrPid
     *
     * @return self
     */
    public function shareNet($nameOrPid)
    {
        $this->option('--share-net', $nameOrPid);
        return $this;
    }

    /**
     * Inherit an IPC namespace from a name container or a pid.
     *
     * @param string|int $nameOrPid
     *
     * @return self
     */
    public function shareIpc($nameOrPid)
    {
        $this->option('--share-ipc', $nameOrPid);
        return $this;
    }

    /**
     * Inherit a UTS namespace from a name container or a pid. The starting LXC
     * will not set the hostname, but the container OS may do it anyway.
     *
     * @param string|int $nameOrPid
     *
     * @return self
     */
    public function shareUts($nameOrPid)
    {
        $this->option('--share-uts', $nameOrPid);
        return $this;
    }

    /**
     * Mute on.
     *
     * @return self
     */
    public function quiet()
    {
        $this->option('-q');
        return $this;
    }

    /**
     * Use an alternate container path. The default is /var/lib/lxc.
     *
     * @param string $path
     *
     * @return self
     */
    public function lxcPath($path)
    {
        $this->option('-P', $path);
        return $this;
    }

    /**
     * Output to an alternate log FILE. The default is no log.
     *
     * @param string $file
     *
     * @return self
     */
    public function logfile($file)
    {
        $this->option('-o', $file);
        return $this;
    }

    /**
     * Set log priority to LEVEL. The default log priority is ERROR. Possible
     * values are : FATAL, CRIT, WARN, ERROR, NOTICE, INFO, DEBUG.
     * Note that this option is setting the priority of the events log in the
     * alternate log file. It do not have effect on the ERROR events log on
     * stderr.
     *
     * @param string $level FATAL|CRIT|WARN|ERROR|NOTICE|INFO|DEBUG.
     *
     * @return self
     */
    public function logPriority($level)
    {
        $this->option('-l', strtoupper($level));
        return $this;
    }
}
