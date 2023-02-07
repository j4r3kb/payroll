<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company\Infrastructure\Query;

use App\Company\Application\Query\DepartmentChoiceView;
use App\Company\Application\Query\DepartmentQuery;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Repository\CompanyRepository;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class DepartmentDbalQueryTest extends KernelTestCase
{
    public function testReturnsChoiceViewWithAllDepartments(): void
    {
        $companyRepository = $this->getContainer()->get(CompanyRepository::class);
        $company = Company::create('Company 1');
        $salaryBonusPolicyId = SalaryBonusPolicyId::fromString(Uuid::v4()->toRfc4122());
        $company->addDepartment('Department 1', $salaryBonusPolicyId);
        $company->addDepartment('Department 2', null);
        $companyRepository->save($company);
        $em = $this->getContainer()->get(EntityManagerInterface::class);
        $em->flush();
        $departmentQuery = $this->getContainer()->get(DepartmentQuery::class);

        $departmentList = $departmentQuery->getDepartmentList();

        $this->assertInstanceOf(DepartmentChoiceView::class, $departmentList);
        $this->assertIsArray($departmentList->choices);
        $this->assertCount(2, $departmentList->choices);
        $this->assertTrue(in_array('Department 1', $departmentList->choices));
        $this->assertTrue(in_array('Department 2', $departmentList->choices));
    }

    public function testReturnsSalaryBonusPolicyId(): void
    {
        $companyRepository = $this->getContainer()->get(CompanyRepository::class);
        $company = Company::create('Company 1');
        $salaryBonusPolicyId = SalaryBonusPolicyId::fromString(Uuid::v4()->toRfc4122());
        $departmentId = $company->addDepartment('Department 1', $salaryBonusPolicyId);
        $companyRepository->save($company);
        $em = $this->getContainer()->get(EntityManagerInterface::class);
        $em->flush();
        $departmentQuery = $this->getContainer()->get(DepartmentQuery::class);

        $this->assertEquals(
            $salaryBonusPolicyId->__toString(),
            $departmentQuery->getDepartmentSalaryBonusPolicyId($departmentId->__toString())
        );
    }
}
