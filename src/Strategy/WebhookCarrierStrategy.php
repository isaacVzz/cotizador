<?php

namespace App\Strategy;

use App\DTO\QuoteRequestDTO;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WebhookCarrierStrategy implements QuoteStrategyInterface
{
    public function __construct(
        private HttpClientInterface $http,
        private string $endpoint,
        private float $price,
        private string $carrierName,
        private bool $simulateFailure,
        private LoggerInterface $logger
    ) {}

    public function quote(QuoteRequestDTO $dto): array
    {
        $this->logger->info('WebhookCarrierStrategy: calling webhook', [
            'carrier' => $this->carrierName,
            'endpoint' => $this->endpoint,
            'origin' => $dto->originZipcode,
            'destination' => $dto->destinationZipcode,
            'simulate_failure' => $this->simulateFailure,
        ]);

        if (empty($this->endpoint)) {
            return [
                'success' => false,
                'error' => 'No endpoint configured for carrier',
                'provider_response' => [
                    'origin' => $dto->originZipcode,
                    'destination' => $dto->destinationZipcode,
                ],
            ];
        }

        try {
            $response = $this->http->request('POST', $this->endpoint, [
                'json' => [
                    'originZipcode' => $dto->originZipcode,
                    'destinationZipcode' => $dto->destinationZipcode,
                ],
                'timeout' => 10 // webhook.site a veces tarda
            ]);

            $status = $response->getStatusCode();
            try {
                $data = $response->toArray(false);
            } catch (\Throwable $e) {
                $data = $response->getContent(false);
            }

            $this->logger->info('WebhookCarrierStrategy: webhook response received', [
                'carrier' => $this->carrierName,
                'status' => $status,
                'endpoint' => $this->endpoint,
            ]);

            if ($this->simulateFailure) {
                return [
                    'success' => false,
                    'error' => 'Provider error - service unavailable',
                    'price' => $this->price,
                    'provider_response' => $data ?? [
                        'origin' => $dto->originZipcode,
                        'destination' => $dto->destinationZipcode,
                        'webhook_status' => $status,
                    ],
                ];
            }

            $isSuccess = $status >= 200 && $status < 300;
            
            return [
                'success' => $isSuccess,
                'price' => $this->price,
                'provider_response' => $data ?? [
                    'origin' => $dto->originZipcode,
                    'destination' => $dto->destinationZipcode,
                    'webhook_status' => $status,
                ],
            ];

        } catch (\Throwable $e) {
            $this->logger->error('WebhookCarrierStrategy: webhook call failed', [
                'carrier' => $this->carrierName,
                'message' => $e->getMessage(),
                'endpoint' => $this->endpoint,
            ]);

            return [
                'success' => false,
                'error' => 'Webhook call failed: ' . $e->getMessage(),
                'provider_response' => [
                    'origin' => $dto->originZipcode,
                    'destination' => $dto->destinationZipcode,
                ],
            ];
        }
    }
}

