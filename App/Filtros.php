<?php

namespace App;

class Filtros
{
    protected $filtros = [];
    protected $parametros = []; // Para binding seguro no PDO

    public function addFiltro(string $coluna, string $operador, $valor): self
    {
        $this->filtros[] = [
            'coluna'   => $coluna,
            'operador' => strtoupper($operador),
            'valor'    => $valor
        ];
        return $this;
    }

    /**
     * Gera a cláusula WHERE e os parâmetros para a consulta SQL.
     * @return array Contém 'where' (string) e 'params' (array).
     */
    public function gerarSqlWhere(): array
    {
        if (empty($this->filtros)) {
            return ['where' => '', 'params' => []];
        }

        $whereParts = [];
        $this->parametros = []; // Reseta os parâmetros a cada geração

        foreach ($this->filtros as $index => $filtro) {
            $placeholder = ":param" . $index;
            $coluna = $filtro['coluna']; // Assumimos que o nome da coluna é seguro
            $operador = $filtro['operador'];

            if ($operador === 'LIKE' || $operador === 'NOT LIKE') {
                $whereParts[] = "`{$coluna}` {$operador} {$placeholder}";
                $this->parametros[$placeholder] = '%' . $filtro['valor'] . '%';
            } else {
                $whereParts[] = "`{$coluna}` {$operador} {$placeholder}";
                $this->parametros[$placeholder] = $filtro['valor'];
            }
        }

        return [
            'where' => ' WHERE ' . implode(' AND ', $whereParts),
            'params' => $this->parametros
        ];
    }
}