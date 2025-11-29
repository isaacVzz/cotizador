<?php

namespace App\Strategy;

use App\DTO\QuoteRequestDTO;

interface QuoteStrategyInterface
{
    /**
     * Realiza la cotización para el carrier y devuelve un array con el resultado.
     *
     * @return array Estructura: ['success' => bool, ...]
     */
    public function quote(QuoteRequestDTO $dto): array;
}
