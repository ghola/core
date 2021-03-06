<?php

/*
 * INCLUDED&MODIFIED BECAUSE THE GITHUB PROJECT SEEMS TO BE DEAD.
 *
 * This file is part of Spork, an OpenSky project.
 *
 * (c) OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace acceptancesupport\PSB\Core\Fork;

use acceptancesupport\PSB\Core\Fork\Exception\ProcessControlException;
use acceptancesupport\PSB\Core\Fork\Util\Error;
use acceptancesupport\PSB\Core\Fork\Util\ExitMessage;

class ProcessManager
{
    private $factory;
    private $debug;
    private $zombieOkay;
    private $signal;

    /** @var Fork[] */
    private $forks;

    public function __construct(Factory $factory = null, $debug = false)
    {
        $this->factory = $factory ?: new Factory();
        $this->debug = $debug;
        $this->zombieOkay = false;
        $this->forks = [];
    }

    public function __destruct()
    {
        if (!$this->zombieOkay) {
            $this->wait();
        }
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function zombieOkay($zombieOkay = true)
    {
        $this->zombieOkay = $zombieOkay;
    }

    /**
     * Forks something into another process and returns a deferred object.
     *
     * @param $callable
     *
     * @return Fork
     */
    public function fork(callable $callable)
    {
        if (-1 === $pid = pcntl_fork()) {
            throw new ProcessControlException('Unable to fork a new process');
        }

        if (0 === $pid) {
            // reset the list of child processes
            $this->forks = [];

            // setup the shared memory
            $shm = $this->factory->createSharedMemory(null, $this->signal);
            $message = new ExitMessage();

            // phone home on shutdown
            register_shutdown_function(
                function () use ($shm, $message) {
                    $status = null;

                    try {
                        $shm->send($message, false);
                    } catch (\Exception $e) {
                        // probably an error serializing the result
                        $message->setResult(null);
                        $message->setError(Error::fromException($e));

                        $shm->send($message, false);

                        exit(2);
                    }
                }
            );

            if (!$this->debug) {
                ob_start();
            }

            try {
                $result = call_user_func($callable, $shm);

                $message->setResult($result);
                $status = is_integer($result) ? $result : 0;
            } catch (\Exception $e) {
                $message->setError(Error::fromException($e));
                $status = 1;
            }

            if (!$this->debug) {
                $message->setOutput(ob_get_clean());
            }

            exit($status);
        }

        // connect to shared memory
        $shm = $this->factory->createSharedMemory($pid);

        return $this->forks[$pid] = $this->factory->createFork($pid, $shm, $this->debug);
    }

    public function wait($hang = true)
    {
        foreach ($this->forks as $fork) {
            $fork->wait($hang);
        }
    }

    public function waitForNext($hang = true)
    {
        if (-1 === $pid = pcntl_wait($status, ($hang ? WNOHANG : 0) | WUNTRACED)) {
            throw new ProcessControlException('Error while waiting for next fork to exit');
        }

        if (isset($this->forks[$pid])) {
            $this->forks[$pid]->processWaitStatus($status);
        }
    }

    public function waitFor($pid, $hang = true)
    {
        if (!isset($this->forks[$pid])) {
            throw new \InvalidArgumentException('There is no fork with PID ' . $pid);
        }

        return $this->forks[$pid]->wait($hang);
    }

    /**
     * Sends a signal to all forks.
     *
     * @param int $signal
     */
    public function killAll($signal = SIGINT)
    {
        foreach ($this->forks as $fork) {
            $fork->kill($signal);
        }
    }
}
