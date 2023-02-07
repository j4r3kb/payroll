<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company\Infrastructure\Query;

use App\Company\Application\Query\CompanyChoiceView;
use App\Company\Application\Query\CompanyQuery;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CompanyDbalQueryTest extends KernelTestCase
{
    public function testReturnsChoiceViewWithAllCompanies(): void
    {
        $companyRepository = $this->getContainer()->get(CompanyRepository::class);
        $companyRepository->save(Company::create('Company 1'));
        $companyRepository->save(Company::create('Company 2'));
        $em = $this->getContainer()->get(EntityManagerInterface::class);
        $em->flush();
        $companyQuery = $this->getContainer()->get(CompanyQuery::class);

        $companyList = $companyQuery->getCompanyList();

        $this->assertInstanceOf(CompanyChoiceView::class, $companyList);
        $this->assertIsArray($companyList->choices);
        $this->assertCount(2, $companyList->choices);
        $this->assertTrue(in_array('Company 1', $companyList->choices));
        $this->assertTrue(in_array('Company 2', $companyList->choices));
    }
}
