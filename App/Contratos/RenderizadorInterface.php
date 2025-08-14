<?php

namespace App\Contratos;

/**
 * Interface para Estratégias de Renderização.
 * Garante que qualquer formato de saída (HTML, JSON, CSV, PDF)
 * possa ser gerado a partir dos dados analisados.
 */
interface RenderizadorInterface
{
    /**
     * Renderiza os dados analisados em um formato específico.
     * @param array $dadosAnalisados Dados estruturados vindos do AnalisadorDeDados.
     * @return string A saída final no formato desejado.
     */
    public function render(array $dadosAnalisados): string;

    /**
     * Opcional: Método para exibir diretamente a saída.
     */
    public function show(): void;
}