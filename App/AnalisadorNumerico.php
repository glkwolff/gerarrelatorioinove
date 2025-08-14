<?php

namespace App;

/**
 * Trait para fornecer um método reutilizável de conversão de valores
 * para o formato numérico (float), lidando com formatos de moeda e milhar.
 */
trait AnalisadorNumerico
{
    /**
     * Limpa e converte um valor para float, tratando formatos numéricos comuns (ex: "1.234,56").
     * @param mixed $value O valor a ser limpo.
     * @return float O valor numérico limpo.
     */
    private function getNumericValue($value): float
    {
        if (!is_scalar($value)) { return 0.0; }
        $valorLimpo = preg_replace('/[^\d,.-]/', '', (string)$value);
        $valorLimpo = str_replace('.', '', $valorLimpo);
        $valorLimpo = preg_replace('/,([^,]*)$/', '.$1', $valorLimpo);
        return floatval($valorLimpo);
    }
}