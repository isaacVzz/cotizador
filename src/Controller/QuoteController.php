<?php

namespace App\Controller;

use App\Service\QuoteService;
use App\DTO\QuoteRequestDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    #[Route('/api/quote', name: 'api_quote', methods: ['POST'])]
    public function quote(Request $request, QuoteService $quoteService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['originZipcode']) || empty($data['destinationZipcode'])) {
            return $this->json([
                'success' => false,
                'error' => 'originZipcode and destinationZipcode are required'
            ], 400);
        }

        $dto = new QuoteRequestDTO(
            originZipcode: $data['originZipcode'],
            destinationZipcode: $data['destinationZipcode']
        );

        $results = $quoteService->quote($dto);

        return $this->json([
            'success' => true,
            'results' => $results
        ], 200);
    }
}
