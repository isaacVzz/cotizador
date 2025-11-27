<?php

namespace App\DTO;

class QuoteRequestDTO
{
    public function __construct(
        public readonly string $originZipcode,
        public readonly string $destinationZipcode,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['originZipcode']) || !isset($data['destinationZipcode'])) {
            throw new \InvalidArgumentException('originZipcode and destinationZipcode are required.');
        }

        return new self(
            originZipcode: $data['originZipcode'],
            destinationZipcode: $data['destinationZipcode']
        );
    }
}
