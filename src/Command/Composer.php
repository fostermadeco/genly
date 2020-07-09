<?php

namespace FosterMade\Genly\Command;

use FosterMade\Genly\Task;
use Symfony\Component\Console\Input\InputInterface;
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
        $this->ignoreValidationErrors();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $argv = $_SERVER['argv'];
        array_splice($argv, 0, 1, ['web']);
        (new Task\Run())($this, $argv);
    }
}
