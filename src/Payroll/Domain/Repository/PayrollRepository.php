<?php

declare(strict_types=1);

namespace App\Payroll\Domain\Repository;

use App\Payroll\Domain\Entity\Payroll;
use App\Payroll\Domain\ValueObject\PayrollId;

interface PayrollRepository
{
    public function save(Payroll $payroll): void;

    public function findOne(PayrollId $payrollId): ?Payroll;
}
