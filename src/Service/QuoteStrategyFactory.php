<?php

namespace App\Service;

use App\Entity\Carrier;
use App\Strategy\QuoteStrategyInterface;
use App\Strategy\WebhookCarrierStrategy;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class QuoteStrategyFactory
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger
    ) {}

    public function createStrategy(Carrier $carrier): QuoteStrategyInterface
    {
        return new WebhookCarrierStrategy(
            $this->httpClient,
            $carrier->getEndpoint() ?? '',
            $carrier->getPrice() ?? 0.0,
            $carrier->getName() ?? 'Unknown',
            !($carrier->isSuccess() ?? true),
            $this->logger
        );
    }
}

