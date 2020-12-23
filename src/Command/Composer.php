<?php

namespace FosterMade\Genly\Command;

use FosterMade\Genly\Process\Process;
use FosterMade\Genly\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Composer extends AbstractCommand
{
    public function supportsCommon(): bool
    {
        return false;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('composer');
        $this->addOption('no-mount', null, InputOption::VALUE_NONE, 'Do not mount user’s .composer folder to /root/.composer');
        $this->ignoreValidationErrors();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $argv = $_SERVER['argv'];
        array_splice($argv, 0, 1, ['web']);

        if (!$input->getOption('no-mount')) {
            $composerHome = trim(Process::fromShellCommandline('composer -ng config home')->mustRun()->getOutput());
            array_unshift($argv, '-v', "{$composerHome}:/root/.composer");
        } else {
            array_splice($argv, array_search('--no-mount', $argv), 1);
        }

        (new Task\Run())($this, $argv);
    }
}
