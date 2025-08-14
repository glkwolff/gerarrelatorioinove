<?php

namespace App\Contratos;

/**
 * Interface para Adaptadores de Fontes de Dados.
 * Garante que qualquer fonte de dados (BD, API, CSV, etc.)
 * possa ser processada pelo AnalisadorDeDados de forma consistente.
 */
interface FonteDeDadosInterface
{
    /**
     * Busca os dados da fonte e os retorna em um formato padronizado.
     * @return array Deve retornar um array com duas chaves: 'header' e 'body'.
     * 'header' é um array com os nomes das colunas.
     * 'body' é um array de arrays, onde cada array interno representa uma linha.
     */
    public function getDados(): array;
}