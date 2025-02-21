<?php

// src/Service/PositionService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PositionService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getPositions(): array
    {
        $response = $this->client->request('GET', 'https://ibillboard.com/api/positions');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Error al obtener las posiciones de trabajo');
        }

        return $response->toArray();
    }

    public function isValidPosition(string $position): bool
    {
        $positions = $this->getPositions();
        return in_array($position, $positions["positions"]);
    }
}