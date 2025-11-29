<?php

namespace App\Service;

use App\DTO\QuoteRequestDTO;
use App\Repository\CarrierRepository;
use Psr\Log\LoggerInterface;

class QuoteService
{
    public function __construct(
        private CarrierRepository $carrierRepo,
        private QuoteStrategyFactory $strategyFactory,
        private LoggerInterface $logger
    ) {}

    public function quote(QuoteRequestDTO $dto): array
    {
        $carriers = $this->carrierRepo->findBy(['active' => true]);
        $results = [];

        $this->logger->info('Starting quote process', [
            'origin' => $dto->originZipcode,
            'destination' => $dto->destinationZipcode,
            'carriers_found' => count($carriers)
        ]);

        foreach ($carriers as $carrier) {
            $strategy = $this->strategyFactory->createStrategy($carrier);
            $quoteResult = $strategy->quote($dto);

            $result = array_merge([
                'carrier' => $carrier->getName(),
            ], $quoteResult);

            $results[] = $result;

            if ($quoteResult['success'] ?? false) {
                $this->logger->info('Carrier quote successful', [
                    'carrier' => $carrier->getName(),
                    'price' => $quoteResult['price'] ?? null
                ]);
            } else {
                $this->logger->warning('Carrier quote failed', [
                    'carrier' => $carrier->getName(),
                    'error' => $quoteResult['error'] ?? 'Unknown error'
                ]);
            }
        }

        $this->logger->info('Quote process completed', [
            'total_results' => count($results)
        ]);

        return $results;
    }
}
