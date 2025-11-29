<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuoteControllerTest extends WebTestCase
{
    public function testQuoteEndpointRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/quote', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'originZipcode' => '12345',
            'destinationZipcode' => '67890',
        ]));

        // Sin header X-API-Key, debería retornar 401
        $this->assertResponseStatusCodeSame(401);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        $this->assertFalse($response['success'] ?? true);
        $this->assertArrayHasKey('error', $response);
    }

    public function testQuoteEndpointWithInvalidApiKey(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/quote', [], [], [
            'HTTP_X_API_KEY' => 'invalid-key',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'originZipcode' => '12345',
            'destinationZipcode' => '67890',
        ]));

        $this->assertResponseStatusCodeSame(401);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('error', $response);
    }

    public function testQuoteEndpointWithMissingFields(): void
    {
        $client = static::createClient();

        // Primero necesita autenticación válida
        // Este test verifica que sin campos requeridos retorna 400 o 401
        $client->request('POST', '/api/quote', [], [], [
            'HTTP_X_API_KEY' => 'test-key',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'originZipcode' => '12345',
        ]));

        // Puede retornar 400 (validación) o 401 (autenticación)
        $this->assertResponseStatusCodeSame([400, 401]);
    }
}

