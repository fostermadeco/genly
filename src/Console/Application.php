<?php

namespace FosterMade\Genly\Console;

use FosterMade\Genly\Command;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('Genly', '0.9.0');
    }

    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new Command\Common(),
            new Command\Initialize(),
            new Command\Up(),
            new Command\Down(),
        ]);
    }
}
