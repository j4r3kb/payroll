<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company\Application\Command;

use App\Company\Application\Command\AddCompanyCommand;
use App\Company\Application\Command\AddCompanyHandler;
use App\Company\Domain\Repository\CompanyRepository;
use App\Company\Domain\ValueObject\CompanyId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddCompanyHandlerTest extends KernelTestCase
{
    public function testCompanyIsAddedToRepository(): void
    {
        $command = new AddCompanyCommand('Test Name');
        $handler = $this->getContainer()->get(AddCompanyHandler::class);
        $companyRepository = $this->getContainer()->get(CompanyRepository::class);

        $handler($command);
        $company = $companyRepository->findOne(CompanyId::fromString($command->createdId()));

        $this->assertIsString($command->createdId());
        $this->assertEquals('Test Name', $company->name);
    }
}
