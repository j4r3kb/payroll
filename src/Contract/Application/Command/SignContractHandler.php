<?php

declare(strict_types=1);

namespace App\Contract\Application\Command;

use App\Common\Application\Command\CommandHandlerInterface;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use App\Contract\Domain\Entity\Contract;
use App\Contract\Domain\Repository\ContractRepository;
use App\Contract\Domain\Service\EmploymentValidator;
use App\Employee\Domain\ValueObject\EmployeeId;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Carbon\CarbonImmutable;

class SignContractHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ContractRepository $contractRepository,
        private readonly EmploymentValidator $employmentValidator
    )
    {
    }

    public function __invoke(SignContractCommand $command): void
    {
        $contract = Contract::sign(
            CompanyId::fromString($command->companyId),
            DepartmentId::fromString($command->departmentId),
            EmployeeId::fromString($command->employeeId),
            $command->salaryBonusPolicyId ? SalaryBonusPolicyId::fromString($command->salaryBonusPolicyId) : null,
            CarbonImmutable::parse($command->effectiveDate),
            $command->terminationDate ? CarbonImmutable::parse($command->terminationDate) : null,
            $command->salaryAmount,
            $command->salaryCurrency,
            $this->employmentValidator
        );

        $this->contractRepository->save($contract);
        $command->setCreatedId($contract->id()->__toString());
    }
}
