<?php

namespace FosterMade\Genly\Command;

use FosterMade\Genly\Config;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class AbstractCommand extends BaseCommand
{
    const COMMON_DOCKER_COMPOSE_PATH = __DIR__.'/../../docker-compose.yml';

    /**
     * @var InputInterface
     */
    public $input;

    /**
     * @var OutputInterface
     */
    public $output;

    /**
     * @var string
     */
    public $cwd;

    /**
     * @var Config
     */
    protected $config;

    public $composeCommand = ['docker-compose'];

    public function supportsCommon(): bool
    {
        return true;
    }

    protected function configure()
    {
        parent::configure();

        $this->addOption('common', 'c', InputOption::VALUE_NONE, 'Execute the command for the common genly services.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('common')) {
            $config = Yaml::parseFile(self::COMMON_DOCKER_COMPOSE_PATH);
            $this->cwd = dirname(realpath(self::COMMON_DOCKER_COMPOSE_PATH));
        } else {
            $file = file_exists('genly.docker-compose.yml') ? 'genly.docker-compose.yml' : 'docker-compose.yml';
            $config = Yaml::parseFile($file);
            $this->cwd = getcwd();
            $this->composeCommand = array_merge($this->composeCommand, ['--file', $file]);
        }

        $this->config = new Config($config);
        $this->input = $input;
        $this->output = $output;
    }
}
