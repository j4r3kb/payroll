<?php

declare(strict_types=1);

namespace App\Tests\Integration\Contract\Application\Command;

use App\Contract\Application\Command\SignContractCommand;
use App\Contract\Application\Command\SignContractHandler;
use App\Contract\Domain\Entity\Contract;
use App\Contract\Domain\Repository\ContractRepository;
use App\Contract\Domain\Service\EmploymentValidator;
use App\Contract\Domain\ValueObject\ContractId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class SignContractHandlerTest extends KernelTestCase
{
    public function testContractIsAddedToRepository(): void
    {
        $container = $this->getContainer();
        $command = new SignContractCommand(
            Uuid::v4()->toRfc4122(),
            Uuid::v4()->toRfc4122(),
            Uuid::v4()->toRfc4122(),
            Uuid::v4()->toRfc4122(),
            '2022-01-01',
            '2022-12-31',
            1000,
            'USD'
        );
        $contractRepository = $container->get(ContractRepository::class);
        $employmentValidator = $this->createMock(EmploymentValidator::class);
        $employmentValidator->method('employeeExists')->willReturn(true);
        $employmentValidator->method('canEmployeeBeHiredByCompany')->willReturn(true);
        $employmentValidator->method('departmentBelongsToCompany')->willReturn(true);
        $handler = new SignContractHandler($contractRepository, $employmentValidator);

        $handler->__invoke($command);
        $contractId = $command->createdId();
        $contract = $contractRepository->findOne(ContractId::fromString($contractId));

        $this->assertIsString($command->createdId());
        $this->assertInstanceOf(Contract::class, $contract);
    }
}
