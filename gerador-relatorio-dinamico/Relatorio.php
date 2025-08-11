<?php

class Relatorio {
    
    protected $html;
    protected $numeroDeColunas = 0;
    private $calculos = [];

    public function __construct() {
        $this->html = "";
    }
    
    public function __destruct() {}

    public function addCalculo(int $colunaIndex, string $tipo, string $label): self {
        $this->calculos[] = [
            'coluna' => $colunaIndex,
            'tipo' => $tipo,
            'label' => $label
        ];
        return $this;
    }

    public function gerarDePdoStatement(string $titulo, PDOStatement $statement, array $footer = []): self {
        $body_associativo = $statement->fetchAll(PDO::FETCH_ASSOC);
        $header = [];
        $body = [];

        if (!empty($body_associativo)) {
            $header = array_keys($body_associativo[0]);
            foreach ($body_associativo as $linha) {
                $body[] = array_values($linha);
            }
        }
        
        $footer[] = "Total de registros encontrados: " . count($body);
        $footer[] = "Gerado em: " . date('d/m/Y');

        $this->init($titulo, $header, $body, $footer);
        
        return $this;
    }

    public function init(string $titulo, array $header, array $body, array $footer) {
        $this->setTitulo($titulo);
        $this->html .= "<table>";
        $this->setHeaderHeader($header);
        $this->setHeaderBody($body);
        $resultadosCalculos = $this->executarCalculos($body);
        
        $this->setHeaderFooter($footer, $resultadosCalculos);

        $this->html .= "</table>";
    }
    
    private function executarCalculos(array $body): array {
        $resultados = [];
        foreach ($this->calculos as $calculo) {
            $soma = 0;
            if ($calculo['tipo'] === 'soma') {
                foreach ($body as $linha) {
                    $valor = $linha[$calculo['coluna']] ?? 0;
                    $valorLimpo = preg_replace('/[^\d,.-]/', '', $valor);
                    $valorLimpo = str_replace(['.', ','], ['', '.'], $valorLimpo);
                    $soma += floatval($valorLimpo);
                }
                $resultados[] = [
                    'coluna' => $calculo['coluna'],
                    'label' => $calculo['label'],
                    'valor' => number_format($soma, 2, ',', '.')
                ];
            }
        }
        return $resultados;
    }
    
    // VERSÃO CORRIGIDA DO MÉTODO DO RODAPÉ
    protected function setHeaderFooter(array $footer, array $calculos = []) {
        if (empty($footer) && empty($calculos)) { return; }

        $this->html .= "<tfoot>";

        // 1. Cria a linha de totais alinhada PRIMEIRO
        if (!empty($calculos)) {
            $celulasTotais = array_fill(0, $this->numeroDeColunas, '');
            foreach ($calculos as $calc) {
                $celulasTotais[$calc['coluna']] = htmlspecialchars($calc['label'] . $calc['valor']);
            }
            $this->html .= "<tr style='font-weight: bold; background-color: #e9ecef;'>";
            foreach ($celulasTotais as $celula) {
                $this->html .= "<td>" . $celula . "</td>";
            }
            $this->html .= "</tr>";
        }

        // 2. Adiciona as linhas de rodapé padrão DEPOIS
        foreach ($footer as $linhaFooter) {
            $this->html .= "<tr><td colspan='" . $this->numeroDeColunas . "'>" . htmlspecialchars($linhaFooter) . "</td></tr>";
        }

        $this->html .= "</tfoot>";
    }
    
    protected function setTitulo(string $titulo) { $this->html = "<h1>" . htmlspecialchars($titulo) . "</h1>"; }
    protected function setHeaderHeader(array $header) { $this->numeroDeColunas = count($header); $this->html .= "<thead><tr>"; foreach ($header as $c) { $this->html .= "<th>" . htmlspecialchars($c) . "</th>"; } $this->html .= "</tr></thead>"; }
    protected function setHeaderBody(array $body) { $this->html .= "<tbody>"; foreach ($body as $l) { $this->html .= "<tr>"; foreach ($l as $c) { $this->html .= "<td>" . htmlspecialchars($c) . "</td>"; } $this->html .= "</tr>"; } $this->html .= "</tbody>"; }
    public function getHtml(): string { return $this->html; }
    public function show(): void { echo $this->getHtml(); }
}