<?php

declare(strict_types=1);

namespace App\Company\Application\Query;

interface DepartmentQuery
{
    public function getDepartmentList(): DepartmentChoiceView;

    public function getDepartmentSalaryBonusPolicyId(string $departmentId): ?string;
}
