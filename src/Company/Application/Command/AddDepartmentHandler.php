<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Common\Application\Command\CommandHandlerInterface;
use App\Company\Domain\Exception\CompanyNotFoundException;
use App\Company\Domain\Repository\CompanyRepository;
use App\Company\Domain\ValueObject\CompanyId;
use App\Policy\Domain\Exception\SalaryBonusPolicyNotFoundException;
use App\Policy\Domain\Repository\SalaryBonusPolicyRepository;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;

class AddDepartmentHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly CompanyRepository $companyRepository,
        private readonly SalaryBonusPolicyRepository $salaryBonusPolicyRepository
    )
    {
    }

    public function __invoke(AddDepartmentCommand $command): void
    {
        $company = $this->companyRepository->findOne(CompanyId::fromString($command->companyId));
        if ($company === null) {
            throw CompanyNotFoundException::create($command->companyId);
        }

        if ($command->salaryBonusPolicyId === null) {
            $departmentId = $company->addDepartment($command->name, $command->salaryBonusPolicyId);
            $command->setCreatedId($departmentId->__toString());

            return;
        }

        $salaryBonusPolicyId = SalaryBonusPolicyId::fromString($command->salaryBonusPolicyId);
        $salaryBonusPolicy = $this->salaryBonusPolicyRepository->findOne($salaryBonusPolicyId);
        if ($salaryBonusPolicy === null) {
            throw SalaryBonusPolicyNotFoundException::create($salaryBonusPolicyId->__toString());
        }

        $departmentId = $company->addDepartment($command->name, $salaryBonusPolicyId);
        $command->setCreatedId($departmentId->__toString());
    }
}
