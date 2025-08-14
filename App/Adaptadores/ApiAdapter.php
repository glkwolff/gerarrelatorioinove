<?php

namespace App\Adaptadores;

require_once __DIR__ . '/../Contratos/FonteDeDadosInterface.php';

use App\Contratos\FonteDeDadosInterface;

class ApiAdapter implements FonteDeDadosInterface
{
    private $apiUrl;

    public function __construct(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function getDados(): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $jsonResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \RuntimeException('Erro ao se comunicar com a API: ' . curl_error($ch));
        }
        curl_close($ch);

        $dadosDaAPI = json_decode($jsonResponse, true);

        if ($dadosDaAPI === null || isset($dadosDaAPI['erro']) || empty($dadosDaAPI)) {
             throw new \RuntimeException("Nenhum dado válido recebido da API.");
        }

        $header = array_keys($dadosDaAPI[0]);
        $body = [];
        foreach ($dadosDaAPI as $linhaAssociativa) {
            $body[] = array_values($linhaAssociativa);
        }

        return ['header' => $header, 'body' => $body];
    }
}