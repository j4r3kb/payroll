<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Query;

use App\Common\Infrastructure\Query\AbstractDbalQuery;
use App\Company\Application\Query\CompanyChoiceView;
use App\Company\Application\Query\CompanyQuery;

class CompanyDbalQuery extends AbstractDbalQuery implements CompanyQuery
{
    public function getCompanyList(): CompanyChoiceView
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('c.id, c.name')
            ->from('company', 'c')
            ->orderBy('c.name')
        ;

        return CompanyChoiceView::create($qb->executeQuery()->fetchAllKeyValue());
    }
}
