<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Query;

use App\Common\Infrastructure\Query\AbstractDbalQuery;
use App\Company\Application\Query\DepartmentChoiceView;
use App\Company\Application\Query\DepartmentQuery;

class DepartmentDbalQuery extends AbstractDbalQuery implements DepartmentQuery
{
    public function getDepartmentList(): DepartmentChoiceView
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('d.id, d.name')
            ->from('company_department', 'd')
            ->orderBy('d.name')
        ;

        return DepartmentChoiceView::create($qb->executeQuery()->fetchAllKeyValue());
    }

    public function getDepartmentSalaryBonusPolicyId(string $departmentId): ?string
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('d.salary_bonus_policy_id')
            ->from('company_department', 'd')
            ->where('d.id = :id')
            ->setParameter('id', $departmentId)
        ;

        return $qb->executeQuery()->fetchOne() ?? null;
    }
}
