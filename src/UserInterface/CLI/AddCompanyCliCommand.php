<?php

declare(strict_types=1);

namespace App\UserInterface\CLI;

use App\Company\Application\Command\AddCompanyCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class AddCompanyCliCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('company:add')
            ->setDescription('Add new company')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $companyName = (string) $style->ask('Enter new company name');

        $this->commandBus->dispatch($command = new AddCompanyCommand($companyName));

        $style->text('Added company with id ' . $command->createdId());

        return CliCommandResult::SUCCESS;
    }
}
