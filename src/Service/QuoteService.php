<?php

namespace App\Service;

use App\DTO\QuoteRequestDTO;
use App\Repository\CarrierRepository;
use Psr\Log\LoggerInterface;

class QuoteService
{
    public function __construct(
        private CarrierRepository $carrierRepo,
        private LoggerInterface $logger
    ) {}

    public function quote(QuoteRequestDTO $dto): array
    {
        // Obtener carriers activos de la base de datos
        $carriers = $this->carrierRepo->findBy(['active' => true]);

        $results = [];

        $this->logger->info('Starting quote process', [
            'origin' => $dto->originZipcode,
            'destination' => $dto->destinationZipcode,
            'carriers_found' => count($carriers)
        ]);

        foreach ($carriers as $carrier) {
            if ($carrier->isSuccess()) {
                $results[] = [
                    'carrier' => $carrier->getName(),
                    'success' => true,
                    'price' => $carrier->getPrice(),
                    'provider_response' => [
                        'origin' => $dto->originZipcode,
                        'destination' => $dto->destinationZipcode
                    ]
                ];

                $this->logger->info('Carrier quote successful', [
                    'carrier' => $carrier->getName(),
                    'price' => $carrier->getPrice()
                ]);

            } else {
                $results[] = [
                    'carrier' => $carrier->getName(),
                    'success' => false,
                    'error' => "Simulated provider error",
                    'provider_response' => [
                        'origin' => $dto->originZipcode,
                        'destination' => $dto->destinationZipcode
                    ]
                ];

                $this->logger->warning('Carrier quote failed', [
                    'carrier' => $carrier->getName()
                ]);
            }
        }

        $this->logger->info('Quote process completed', [
            'total_results' => count($results)
        ]);

        return $results;
    }
}
