<?php

declare(strict_types=1);

namespace App\Employee\Application\Query;

use DateTimeInterface;

interface EmployeeQuery
{
    public function findEmployeesNotHiredByCompany(
        string $companyId,
        DateTimeInterface $newContractEffectiveDate,
        ?DateTimeInterface $newContractTerminationDate
    ): EmployeeChoiceView;
}
