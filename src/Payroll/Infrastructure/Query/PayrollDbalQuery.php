<?php

declare(strict_types=1);

namespace App\Payroll\Infrastructure\Query;

use App\Common\Application\Query\Filter;
use App\Common\Application\Query\Sort;
use App\Common\Infrastructure\Query\AbstractDbalQuery;
use App\Payroll\Application\Query\PayrollChoiceView;
use App\Payroll\Application\Query\PayrollItemView;
use App\Payroll\Application\Query\PayrollQuery;
use App\Payroll\Application\Query\PayrollView;

class PayrollDbalQuery extends AbstractDbalQuery implements PayrollQuery
{
    public function getPayroll(string $payrollId, ?Sort $sort = null, ?Filter $filter = null): ?PayrollView
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select(implode(', ', array_keys(static::availableColumns())))
            ->from('payroll_item')
            ->where('payroll_id = :payrollId')
            ->setParameter('payrollId', $payrollId)
        ;
        if ($filter && array_key_exists($filter->fieldName, static::availableColumns())) {
            $qb->andWhere(
                $qb->expr()->like($filter->fieldName, ':like')
            )
                ->setParameter('like', sprintf('%%%s%%', $filter->like))
            ;
        }
        if ($sort && array_key_exists($sort->fieldName, static::availableColumns())) {
            $qb->orderBy($sort->fieldName, $sort->sortOrder->value);
        }

        $items = [];
        foreach ($qb->executeQuery()->fetchAllAssociative() as $row) {
            $items[] = PayrollItemView::fromArray(array_values($row));
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->select('p.company_name, p.year, p.month, p.created_at')
            ->from('payroll', 'p')
            ->where('p.id = :payrollId')
            ->setParameter('payrollId', $payrollId)
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        if ($result === []) {
            return null;
        }

        return PayrollView::create(
            $result['company_name'],
            $result['year'],
            $result['month'],
            $result['created_at'],
            $items
        );
    }

    public static function availableColumns(): array
    {
        return [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'department_name' => 'Department name',
            'salary_base' => 'Salary base',
            'salary_bonus' => 'Salary bonus',
            'salary_total' => 'Salary total',
            'salary_currency' => 'Salary currency',
            'salary_bonus_type' => 'Salary bonus type',
        ];
    }

    public function getPayrollListByCompanyAndPeriod(string $companyId, int $year, int $month): PayrollChoiceView
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('p.id, p.created_at')
            ->from('payroll', 'p')
            ->where('p.company_id = :companyId')
            ->andWhere('p.year = :year')
            ->andWhere('p.month = :month')
            ->orderBy('p.created_at', 'DESC')
            ->setParameters(
                [
                    'companyId' => $companyId,
                    'year' => $year,
                    'month' => $month,
                ]
            )
        ;

        return PayrollChoiceView::create($qb->executeQuery()->fetchAllKeyValue());
    }

    public function getCountByCompanyAndPeriod(string $companyId, int $year, int $month): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(p.id)')
            ->from('payroll', 'p')
            ->where('p.company_id = :companyId')
            ->andWhere('p.year = :year')
            ->andWhere('p.month = :month')
            ->setParameters(
                [
                    'companyId' => $companyId,
                    'year' => $year,
                    'month' => $month,
                ]
            )
        ;

        return (int) $qb->executeQuery()->fetchOne();
    }
}
