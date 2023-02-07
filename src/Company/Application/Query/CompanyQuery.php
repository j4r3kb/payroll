<?php

declare(strict_types=1);

namespace App\Company\Application\Query;

interface CompanyQuery
{
    public function getCompanyList(): CompanyChoiceView;
}
