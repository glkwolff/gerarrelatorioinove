<?php

namespace App\Renderizadores;

require_once __DIR__ . '/../Contratos/RenderizadorInterface.php';

use App\Contratos\RenderizadorInterface;

class GeradorJson implements RenderizadorInterface
{
    private $json;

    public function render(array $dadosAnalisados): string
    {
        // Remove o corpo detalhado para uma resposta JSON mais limpa,
        // focando nos resultados e metadados.
        unset($dadosAnalisados['body']);

        $this->json = json_encode($dadosAnalisados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $this->json;
    }

    public function show(): void
    {
        header('Content-Type: application/json');
        echo $this->json;
    }
}