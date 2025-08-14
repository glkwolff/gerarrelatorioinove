<?php

namespace App\Renderizadores;

require_once __DIR__ . '/../Contratos/RenderizadorInterface.php';

use App\Contratos\RenderizadorInterface;

class GeradorHtml implements RenderizadorInterface
{
    protected string $html = '';
    protected int $numeroDeColunas = 0;

    public function render(array $dadosAnalisados): string
    {
        $this->gerar($dadosAnalisados);
        return $this->html;
    }

    public function show(): void
    {
        echo $this->html;
    }

    /**
     * Orquestra a construção do HTML do relatório.
     * @param array $dadosAnalisados
     * @return self
     */
    public function gerar(array $dadosAnalisados): self
    {
        $this->html = ''; // Reseta o HTML
        $titulo = htmlspecialchars($dadosAnalisados['titulo'] ?? 'Relatório');
        $header = $dadosAnalisados['header'] ?? [];
        $body = $dadosAnalisados['body'] ?? [];
        $resultados = $dadosAnalisados['resultados'] ?? [];

        $this->numeroDeColunas = count($header);

        $this->html .= "<h1>{$titulo}</h1>";
        $this->html .= "<table>";
        $this->html .= $this->gerarCabecalhoTabela($header);
        $this->html .= $this->gerarCorpoTabela($body);
        $this->html .= $this->gerarRodapeTabela($resultados);
        $this->html .= "</table>";

        return $this;
    }

    private function gerarCabecalhoTabela(array $header): string
    {
        if (empty($header)) {
            return '';
        }
        $html = "<thead><tr>";
        foreach ($header as $coluna) {
            $html .= "<th>" . htmlspecialchars($coluna) . "</th>";
        }
        $html .= "</tr></thead>";
        return $html;
    }

    private function gerarCorpoTabela(array $body): string
    {
        if (empty($body)) {
            return "<tbody><tr><td colspan='" . ($this->numeroDeColunas ?: 1) . "'>Nenhum registro encontrado.</td></tr></tbody>";
        }

        $html = "<tbody>";
        foreach ($body as $linha) {
            $html .= "<tr>";
            foreach ($linha as $celula) {
                $html .= "<td>" . htmlspecialchars((string)$celula) . "</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        return $html;
    }

    private function gerarRodapeTabela(array $resultados): string
    {
        if (empty(array_filter($resultados)) || $this->numeroDeColunas === 0) {
            return '';
        }

        $html = "<tfoot>";
        foreach ($resultados as $tipo => $calculos) {
            if (empty($calculos)) continue;

            foreach ($calculos as $calculo) {
                // Prepara um array de células vazias para a linha do rodapé
                $celulas = array_fill(0, $this->numeroDeColunas, ['content' => '', 'style' => '']);
                
                $colunaAlvo = $calculo['coluna'];
                $label = htmlspecialchars($calculo['label']);
                $valor = $this->formatarValorResultado($calculo['valor']);

                // Coloca o valor do cálculo na coluna de destino
                if (isset($celulas[$colunaAlvo])) {
                    $celulas[$colunaAlvo]['content'] = $valor;
                }

                // Coloca o rótulo na coluna anterior à de destino, se houver espaço
                if ($colunaAlvo > 0 && isset($celulas[$colunaAlvo - 1])) {
                    $celulas[$colunaAlvo - 1]['content'] = "<strong>{$label}</strong>";
                    $celulas[$colunaAlvo - 1]['style'] = "text-align: right; padding-right: 5px;";
                } else {
                    // Se não houver espaço (coluna 0), anexa o rótulo ao valor
                    $celulas[$colunaAlvo]['content'] = "<strong>{$label}</strong> " . $celulas[$colunaAlvo]['content'];
                }

                // Constrói a linha <tr> com as células preparadas
                $html .= "<tr>";
                foreach ($celulas as $celula) {
                    $styleAttr = !empty($celula['style']) ? " style='{$celula['style']}'" : '';
                    $html .= "<td{$styleAttr}>{$celula['content']}</td>";
                }
                $html .= "</tr>";
            }
        }
        $html .= "</tfoot>";
        return $html;
    }

    private function formatarValorResultado($valor): string
    {
        if (is_array($valor)) return htmlspecialchars(implode(', ', $valor));
        if (is_float($valor)) return number_format($valor, 2, ',', '.');
        return htmlspecialchars((string)$valor);
    }
}
