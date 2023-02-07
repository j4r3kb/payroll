<?php

declare(strict_types=1);

namespace App\Employee\Infrastructure\Query;

use App\Common\Infrastructure\Query\AbstractDbalQuery;
use App\Employee\Application\Query\EmployeeChoiceView;
use App\Employee\Application\Query\EmployeeQuery;
use DateTimeInterface;

class EmployeeDbalQuery extends AbstractDbalQuery implements EmployeeQuery
{
    public function findEmployeesNotHiredByCompany(
        string $companyId,
        DateTimeInterface $newContractEffectiveDate,
        ?DateTimeInterface $newContractTerminationDate
    ): EmployeeChoiceView
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('e.id, e.first_name || " " ||  e.last_name AS full_name')
            ->from('employee', 'e')
            ->leftJoin(
                'e',
                'contract',
                'c',
                'e.id = c.employee_id AND c.company_id = :companyId'
            )
            ->where('c.termination_date IS NOT NULL')
            ->andWhere('c.termination_date < :effectiveDate')
            ->orWhere('c.company_id IS NULL')
            ->orderBy('e.first_name')
            ->setParameters(
                [
                    'companyId' => $companyId,
                    'effectiveDate' => $newContractEffectiveDate,
                ]
            )
        ;

        if ($newContractTerminationDate !== null) {
            $qb->orWhere('c.effective_date > :terminationDate')
                ->setParameter('terminationDate', $newContractTerminationDate)
            ;
        }

        return EmployeeChoiceView::create($qb->executeQuery()->fetchAllKeyValue());
    }
}
