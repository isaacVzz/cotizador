<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private string $apiKey
    ) {}

    public function supports(Request $request): ?bool
    {
        // Autenticar todas las rutas que empiecen con /api/
        // Si no hay header, aún debemos intentar autenticar para devolver error 401
        return str_starts_with($request->getPathInfo(), '/api/');
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-API-Key');

        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('API key is required');
        }

        if ($apiKey !== $this->apiKey) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        // Crear un usuario anónimo con la API key válida
        return new SelfValidatingPassport(
            new UserBadge($apiKey, fn() => new ApiKeyUser($apiKey))
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Permitir que la petición continúe
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response(
            json_encode([
                'success' => false,
                'error' => 'Authentication failed: ' . $exception->getMessage()
            ]),
            Response::HTTP_UNAUTHORIZED,
            ['Content-Type' => 'application/json']
        );
    }
}

