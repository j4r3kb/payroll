<?php

declare(strict_types=1);

namespace App\UserInterface\CLI;

use App\Employee\Application\Command\AddEmployeeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class AddEmployeeCliCommand extends Command
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
            ->setName('employee:add')
            ->setDescription('Add new employee')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $firstName = (string) $style->ask('Enter employee\'s first name');
        $lastName = (string) $style->ask('Enter employee\'s last name');

        $this->commandBus->dispatch($command = new AddEmployeeCommand($firstName, $lastName));

        $style->text('Added employee with id ' . $command->createdId());

        return CliCommandResult::SUCCESS;
    }
}
