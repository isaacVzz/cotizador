<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ApiKeyUser implements UserInterface
{
    public function __construct(
        private string $apiKey
    ) {}

    public function getRoles(): array
    {
        return ['ROLE_API'];
    }

    public function eraseCredentials(): void
    {
        // No hay credenciales que borrar
    }

    public function getUserIdentifier(): string
    {
        return $this->apiKey;
    }
}

