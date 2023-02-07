<?php

declare(strict_types=1);

namespace App\Policy\Application\Command;

use App\Common\Application\Command\CommandHandlerInterface;
use App\Policy\Domain\Repository\SalaryBonusPolicyRepository;

class AddSalaryBonusPolicyHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly SalaryBonusPolicyRepository $salaryBonusPolicyRepository
    )
    {
    }

    public function __invoke(AddSalaryBonusPolicyCommand $command): void
    {
        $salaryBonusPolicy = $command->salaryBonusPolicyClass::create(...$command->parameters);
        $this->salaryBonusPolicyRepository->save($salaryBonusPolicy);
        $command->setCreatedId($salaryBonusPolicy->id()->__toString());
    }
}
