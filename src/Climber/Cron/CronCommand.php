<?php

namespace Peak\Climber\Cron;

use Peak\Climber\CommandWithDb;
use Peak\Climber\Cron\Exception\DatabaseNotFoundException;
use Peak\Climber\Cron\Exception\TablesNotFoundException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class CronCommand extends CommandWithDb
{
    /**
     * Initializes the command just after the input has been validated.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // run some validation for cron system
        if (!Cron::hasDbConnection($this->conn)) {
            throw new DatabaseNotFoundException('No connection to a database has been found!');
        } elseif (!Cron::isInstalled($this->conn) && $this->getName() !== 'cron:install') {
            throw new TablesNotFoundException('Cron system is not installed. Please, use command cron:install before using cron commands');
        }
    }
}
