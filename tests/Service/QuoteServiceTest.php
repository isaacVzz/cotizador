<?php

namespace App\Tests\Service;

use App\DTO\QuoteRequestDTO;
use App\Entity\Carrier;
use App\Repository\CarrierRepository;
use App\Service\QuoteService;
use App\Service\QuoteStrategyFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class QuoteServiceTest extends TestCase
{
    private QuoteService $quoteService;
    private CarrierRepository|MockObject $carrierRepository;
    private QuoteStrategyFactory|MockObject $strategyFactory;
    private LoggerInterface|MockObject $logger;

    protected function setUp(): void
    {
        $this->carrierRepository = $this->createMock(CarrierRepository::class);
        $this->strategyFactory = $this->createMock(QuoteStrategyFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->quoteService = new QuoteService(
            $this->carrierRepository,
            $this->strategyFactory,
            $this->logger
        );
    }

    public function testQuoteWithActiveCarriers(): void
    {
        // Arrange
        $dto = new QuoteRequestDTO(
            originZipcode: '12345',
            destinationZipcode: '67890'
        );

        $carrier1 = new Carrier();
        $carrier1->setName('Carrier 1');
        $carrier1->setActive(true);

        $carrier2 = new Carrier();
        $carrier2->setName('Carrier 2');
        $carrier2->setActive(true);

        $carriers = [$carrier1, $carrier2];

        $this->carrierRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['active' => true])
            ->willReturn($carriers);

        $strategy1 = $this->createMock(\App\Strategy\QuoteStrategyInterface::class);
        $strategy1->expects($this->once())
            ->method('quote')
            ->with($dto)
            ->willReturn([
                'success' => true,
                'price' => 100.50,
                'provider_response' => ['origin' => '12345', 'destination' => '67890']
            ]);

        $strategy2 = $this->createMock(\App\Strategy\QuoteStrategyInterface::class);
        $strategy2->expects($this->once())
            ->method('quote')
            ->with($dto)
            ->willReturn([
                'success' => false,
                'error' => 'Provider error',
                'provider_response' => ['origin' => '12345', 'destination' => '67890']
            ]);

        $this->strategyFactory
            ->expects($this->exactly(2))
            ->method('createStrategy')
            ->willReturnOnConsecutiveCalls($strategy1, $strategy2);

        $this->logger->expects($this->atLeastOnce())->method('info');
        $this->logger->expects($this->atLeastOnce())->method('warning');

        // Act
        $results = $this->quoteService->quote($dto);

        // Assert
        $this->assertCount(2, $results);
        $this->assertEquals('Carrier 1', $results[0]['carrier']);
        $this->assertTrue($results[0]['success']);
        $this->assertEquals(100.50, $results[0]['price']);
        $this->assertEquals('Carrier 2', $results[1]['carrier']);
        $this->assertFalse($results[1]['success']);
        $this->assertArrayHasKey('error', $results[1]);
    }

    public function testQuoteWithNoActiveCarriers(): void
    {
        // Arrange
        $dto = new QuoteRequestDTO(
            originZipcode: '12345',
            destinationZipcode: '67890'
        );

        $this->carrierRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['active' => true])
            ->willReturn([]);

        $this->logger->expects($this->atLeastOnce())->method('info');

        // Act
        $results = $this->quoteService->quote($dto);

        // Assert
        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }
}

