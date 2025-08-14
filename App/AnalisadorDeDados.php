<?php

namespace App;

use PDOStatement;
// As classes Calculos e AnalisadorNumerico estão no mesmo namespace (App).
// Com um autoloader (como o do Composer), estas chamadas require_once não são necessárias.
// Elas serão carregadas automaticamente quando AnalisadorDeDados for instanciado ou estendido.
// Se você não estiver usando um autoloader, certifique-se de que Calculos.php e AnalisadorNumerico.php
// sejam carregados ANTES de AnalisadorDeDados.php no seu script principal (ex: relatorio-via-bd.php).

/**
 * Responsável por receber dados brutos e executar cálculos (somas, porcentagens, etc).
 * Retorna dados estruturados, sem se preocupar com a exibição (HTML, JSON, etc).
 */
class AnalisadorDeDados extends Calculos
{
    use AnalisadorNumerico;

    public function analisarDePdoStatement(PDOStatement $statement): array
    {
        $body_associativo = $statement->fetchAll(PDO::FETCH_ASSOC);
        $header = [];
        $body = [];

        if (!empty($body_associativo)) {
            $header = array_keys($body_associativo[0]);
            foreach ($body_associativo as $linha) {
                $body[] = array_values($linha);
            }
        }

        return $this->analisar($header, $body);
    }

    public function analisar(array $header, array $body): array
    {
        $resultadosSomas = !empty($this->somas) ? $this->executarSomas($body) : [];
        $resultadosPorcentagens = !empty($this->porcentagens) ? $this->executarPorcentagens($body) : [];
        $resultadosMedias = !empty($this->medias) ? $this->executarMedias($body) : [];
        $resultadosMedianas = !empty($this->medianas) ? $this->executarMedianas($body) : [];
        $resultadosModas = !empty($this->modas) ? $this->executarModas($body) : [];

        return [
            'header' => $header,
            'body' => $body,
            'resultados' => [
                'somas' => $resultadosSomas,
                'porcentagens' => $resultadosPorcentagens,
                'medias' => $resultadosMedias,
                'medianas' => $resultadosMedianas,
                'modas' => $resultadosModas,
            ],
            'metadados' => [
                'total_registros' => count($body),
                'gerado_em' => date('d/m/Y H:i:s')
            ]
        ];
    }

    private function executarSomas(array $body): array
    {
        $resultados = [];
        foreach ($this->somas as $somaConfig) {
            $soma = 0;
            foreach ($body as $linha) { // Usando a nova função auxiliar para parsing numérico
                $soma += $this->getNumericValue($linha[$somaConfig['coluna']] ?? 0);
            }
            $resultados[] = array_merge($somaConfig, ['valor' => $soma]);
        }
        return $resultados;
    }

    private function executarPorcentagens(array $body): array
    {
        $resultados = [];
        $totalLinhas = count($body);
        if ($totalLinhas === 0) {
            return [];
        }

        foreach ($this->porcentagens as $porc) {
            $contador = 0;
            foreach ($body as $linha) {
                if (isset($linha[$porc['coluna']]) && $linha[$porc['coluna']] === $porc['valor']) {
                    $contador++;
                }
            }
            $percentual = ($contador / $totalLinhas) * 100;
            $resultados[] = array_merge($porc, ['valor' => $percentual, 'contagem' => $contador]);
        }
        return $resultados;
    }

    private function executarMedias(array $body): array
    {
        $resultados = [];
        foreach ($this->medias as $mediaConfig) {
            $valoresNumericos = [];
            foreach ($body as $linha) {
                $valor = $linha[$mediaConfig['coluna']] ?? null;
                if ($valor !== null) {
                    $numericValue = $this->getNumericValue($valor);
                    $valoresNumericos[] = $numericValue;
                }
            }

            $media = 0;
            if (!empty($valoresNumericos)) {
                $media = array_sum($valoresNumericos) / count($valoresNumericos);
            }
            $resultados[] = array_merge($mediaConfig, ['valor' => $media]);
        }
        return $resultados;
    }

    private function executarMedianas(array $body): array
    {
        $resultados = [];
        foreach ($this->medianas as $medianaConfig) {
            $valoresNumericos = [];
            foreach ($body as $linha) {
                $valor = $linha[$medianaConfig['coluna']] ?? null;
                if ($valor !== null) {
                    $numericValue = $this->getNumericValue($valor);
                    $valoresNumericos[] = $numericValue;
                }
            }

            $mediana = 0;
            if (!empty($valoresNumericos)) {
                sort($valoresNumericos); // Ordena os valores
                $count = count($valoresNumericos);
                $middleIndex = floor($count / 2);

                if ($count % 2 === 0) { // Número par de elementos
                    $mediana = ($valoresNumericos[$middleIndex - 1] + $valoresNumericos[$middleIndex]) / 2;
                } else { // Número ímpar de elementos
                    $mediana = $valoresNumericos[$middleIndex];
                }
            }
            $resultados[] = array_merge($medianaConfig, ['valor' => $mediana]);
        }
        return $resultados;
    }

    private function executarModas(array $body): array
    {
        $resultados = [];
        foreach ($this->modas as $modaConfig) {
            $frequencias = [];
            foreach ($body as $linha) {
                $valor = $linha[$modaConfig['coluna']] ?? null;
                if ($valor !== null) {
                    // Para moda, podemos contar a frequência de qualquer tipo de valor (string, int, etc.)
                    $stringValue = (string)$valor;
                    $frequencias[$stringValue] = ($frequencias[$stringValue] ?? 0) + 1;
                }
            }

            $moda = [];
            if (!empty($frequencias)) {
                $maxFrequencia = 0;
                foreach ($frequencias as $valor => $frequencia) {
                    if ($frequencia > $maxFrequencia) {
                        $maxFrequencia = $frequencia;
                    }
                }

                // Coleta todos os valores que têm a frequência máxima
                foreach ($frequencias as $valor => $frequencia) {
                    if ($frequencia === $maxFrequencia) {
                        $moda[] = $valor;
                    }
                }
                // Se todos os valores têm a mesma frequência (ex: todos são únicos), não há uma moda distinta.
                if ($maxFrequencia === 1 && count($frequencias) === count($body)) {
                    $moda = []; // Indica que não há moda distinta
                }
            }
            $resultados[] = array_merge($modaConfig, ['valor' => $moda]);
        }
        return $resultados;
    }
}