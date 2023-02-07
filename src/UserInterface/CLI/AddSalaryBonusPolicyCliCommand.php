<?php

declare(strict_types=1);

namespace App\UserInterface\CLI;

use App\Policy\Application\Command\AddSalaryBonusPolicyCommand;
use App\Policy\Domain\Enum\SalaryBonusPolicyType;
use App\Policy\Domain\Factory\SalaryBonusPolicyClassFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class AddSalaryBonusPolicyCliCommand extends Command
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
            ->setName('company:salary-bonus-policy:add')
            ->setDescription('Add new salary bonus policy')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $policyTypeChoice = $style->choice(
            'Choose salary bonus policy type',
            array_column(SalaryBonusPolicyType::cases(), 'value')
        );

        $salaryBonusPolicyType = SalaryBonusPolicyType::tryFrom($policyTypeChoice);
        $salaryBonusPolicyClass = SalaryBonusPolicyClassFactory::getClassByType($salaryBonusPolicyType);
        $policyParameters = $this->askForPolicyParameters($salaryBonusPolicyClass::parameters(), $style);

        $this->commandBus->dispatch(
            $command = new AddSalaryBonusPolicyCommand($salaryBonusPolicyClass, $policyParameters)
        );

        $style->text('Added policy with id ' . $command->createdId());

        return CliCommandResult::SUCCESS;
    }

    private function askForPolicyParameters(array $policyParameters, SymfonyStyle $style): array
    {
        foreach ($policyParameters as $parameterName => $parameterType) {
            $value = $style->ask(
                sprintf('Enter %s (%s)', $this->camelCaseToWords($parameterName), $parameterType)
            );
            settype($value, $parameterType);
            $policyParameters[$parameterName] = $value;
        }

        return $policyParameters;
    }

    private function camelCaseToWords(string $camelCaseString): string
    {
        $words = preg_split('/(?=[A-Z])/', $camelCaseString);
        $words = array_map('strtolower', $words);

        return implode(' ', $words);
    }
}
