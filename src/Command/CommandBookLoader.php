<?php

namespace App\Command;

use App\Services\ParserBook;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'parse:book', description: 'Loading data into the database.')]
class CommandBookLoader extends Command
{
    public function __construct(
        private readonly ParserBook $parserBook
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $this->parserBook->load($path);
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'The path to the file');
    }
}
