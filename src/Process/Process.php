<?php

namespace FosterMade\Genly\Process;

use Symfony\Component\Process\Process as BaseProcess;

class Process extends BaseProcess
{
    public static function create(array $command, $cwd = null, bool $pty = true, bool $tty = true): BaseProcess
    {
        $process = new BaseProcess($command, $cwd);

        if (true === $pty && BaseProcess::isPtySupported()) {
            $process->setPty($pty);
        }

        if (true === $tty && BaseProcess::isTtySupported()) {
            $process->setTty($tty);
        }

        return $process;
    }
}
