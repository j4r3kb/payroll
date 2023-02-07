<?php

declare(strict_types=1);

namespace App\UserInterface\CLI;

use App\Company\Application\Command\AddDepartmentCommand;
use App\Company\Application\Query\CompanyQuery;
use App\Policy\Domain\Repository\SalaryBonusPolicyRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class AddDepartmentCliCommand extends Command
{
    public function __construct(
        private readonly CompanyQuery $companyQuery,
        private readonly SalaryBonusPolicyRepository $salaryBonusPolicyRepository,
        private readonly MessageBusInterface $commandBus
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('company:department:add')
            ->setDescription('Add new department to company')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $companyId = $style->choice(
            'Select company',
            $this->companyQuery->getCompanyList()->choices
        );

        $departmentName = (string) $style->ask('Enter new department name');

        $salaryBonusPolicyId = $style->choice(
            'Select salary bonus policy',
            $this->salaryBonusPolicyChoiceList()
        );
        $salaryBonusPolicyId = $salaryBonusPolicyId !== '0' ? $salaryBonusPolicyId : null;

        $this->commandBus->dispatch(
            $command = new AddDepartmentCommand($companyId, $departmentName, $salaryBonusPolicyId)
        );

        $style->text('Added department with id ' . $command->createdId());

        return CliCommandResult::SUCCESS;
    }

    private function salaryBonusPolicyChoiceList(): array
    {
        $choiceList = [0 => 'No bonus policy'];
        $salaryBonusPolicyList = $this->salaryBonusPolicyRepository->all();
        foreach ($salaryBonusPolicyList as $policy) {
            $choiceList[$policy->id()->__toString()] = $policy->name();
        }

        return $choiceList;
    }
}
