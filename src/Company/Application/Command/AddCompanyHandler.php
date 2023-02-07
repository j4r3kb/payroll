<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Common\Application\Command\CommandHandlerInterface;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Repository\CompanyRepository;

class AddCompanyHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly CompanyRepository $companyRepository
    )
    {
    }

    public function __invoke(AddCompanyCommand $command): void
    {
        $company = Company::create($command->companyName);
        $this->companyRepository->save($company);
        $command->setCreatedId($company->id()->__toString());
    }
}
