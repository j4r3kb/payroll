<?php

declare(strict_types=1);

namespace App\Tests\Integration\Policy\Application\Command;

use App\Policy\Application\Command\AddSalaryBonusPolicyCommand;
use App\Policy\Application\Command\AddSalaryBonusPolicyHandler;
use App\Policy\Domain\Entity\PercentageSalaryBonusPolicy;
use App\Policy\Domain\Repository\SalaryBonusPolicyRepository;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddSalaryBonusPolicyHandlerTest extends KernelTestCase
{
    public function testSalaryBonusPolicyIsAddedToRepository(): void
    {
        $container = $this->getContainer();
        $salaryBonusPolicyRepository = $container->get(SalaryBonusPolicyRepository::class);
        $handler = $container->get(AddSalaryBonusPolicyHandler::class);
        $command = new AddSalaryBonusPolicyCommand(
            PercentageSalaryBonusPolicy::class,
            ['ratePerHundred' => 100]
        );

        $handler->__invoke($command);

        $this->assertInstanceOf(
            PercentageSalaryBonusPolicy::class,
            $salaryBonusPolicyRepository->findOne(SalaryBonusPolicyId::fromString($command->createdId()))
        );
    }
}
