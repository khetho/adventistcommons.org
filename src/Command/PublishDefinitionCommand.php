<?php

namespace App\Command;

use App\Definition\Publisher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PublishDefinitionCommand
 * @package App\Command
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PublishDefinitionCommand extends Command
{
    private $publisher;

    public function __construct(Publisher $publisher, string $name = null)
    {
        $this->publisher = $publisher;
        parent::__construct($name);
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:definitions:publish';

    protected function configure()
    {
        $this
            ->setDescription('Publish definition for translation editor frontend.')
            ->setHelp('This command export all definitions in javascript glossary')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->publisher->publishAll();
    }
}
