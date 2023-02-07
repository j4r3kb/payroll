<?php

declare(strict_types=1);

namespace App\Payroll\Application\Command;

use App\Common\Application\Command\CommandHandlerInterface;
use App\Company\Domain\ValueObject\CompanyId;
use App\Payroll\Domain\Service\PayrollGenerator;
use App\Payroll\Domain\ValueObject\PayrollPeriod;

class GeneratePayrollHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly PayrollGenerator $payrollGenerator
    )
    {
    }

    public function __invoke(GeneratePayrollCommand $command): void
    {
        $this->payrollGenerator->generate(
            CompanyId::fromString($command->companyId), PayrollPeriod::create($command->year, $command->month)
        );
    }
}
