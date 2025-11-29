<?php

namespace App\Tests\Service;

use App\Entity\Carrier;
use App\Service\QuoteStrategyFactory;
use App\Strategy\WebhookCarrierStrategy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class QuoteStrategyFactoryTest extends TestCase
{
    private QuoteStrategyFactory $factory;
    private HttpClientInterface|MockObject $httpClient;
    private LoggerInterface|MockObject $logger;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->factory = new QuoteStrategyFactory(
            $this->httpClient,
            $this->logger
        );
    }

    public function testCreateStrategy(): void
    {
        $carrier = new Carrier();
        $carrier->setName('Test Carrier');
        $carrier->setPrice(150.00);
        $carrier->setEndpoint('https://webhook.site/test');
        $carrier->setSuccess(true);

        $strategy = $this->factory->createStrategy($carrier);

        $this->assertInstanceOf(WebhookCarrierStrategy::class, $strategy);
    }
}

