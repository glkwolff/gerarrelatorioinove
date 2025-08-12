<?php

class Relatorio {
    
    protected $html;
    protected $numeroDeColunas = 0;
    private $calculos = [];
    private $porcentagens = [];

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

    public function addPorcentagem(int $colunaIndex, string $valorAlvo, string $label): self {
        $this->porcentagens[] = [
            'coluna' => $colunaIndex,
            'valor' => $valorAlvo,
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
        $resultadosPorcentagens = $this->executarPorcentagens($body);
        
        $this->setHeaderFooter($footer, $resultadosCalculos, $resultadosPorcentagens);

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

    private function executarPorcentagens(array $body): array {
        $resultados = [];
        if (empty($body)) {
            return [];
        }
        $totalLinhas = count($body);

        foreach ($this->porcentagens as $porc) {
            $contador = 0;
            foreach ($body as $linha) {
                if (isset($linha[$porc['coluna']]) && $linha[$porc['coluna']] === $porc['valor']) {
                    $contador++;
                }
            }
            $percentual = ($totalLinhas > 0) ? ($contador / $totalLinhas) * 100 : 0;
            
            $resultados[] = [
                'coluna' => $porc['coluna'],
                'label' => $porc['label'],
                'valor' => number_format($percentual, 2, ',', '.') . '%'
            ];
        }
        return $resultados;
    }
    
    /**
     * ATUALIZADO: Agrupa os cálculos em linhas, colocando-os lado a lado sempre que possível.
     */
    protected function setHeaderFooter(array $footer, array $calculos = [], array $porcentagens = []) {
        $todosOsCalculos = array_merge($porcentagens, $calculos);
        if (empty($footer) && empty($todosOsCalculos)) { return; }

        $this->html .= "<tfoot>";
        
        // --- LÓGICA DE AGRUPAMENTO ---
        if (!empty($todosOsCalculos)) {
            $linhasDeCalculo = [];
            $colunasOcupadasNaLinha = [];
            $indiceLinhaAtual = 0;

            foreach ($todosOsCalculos as $calc) {
                // Se a linha ainda não existe, crie-a
                if (!isset($linhasDeCalculo[$indiceLinhaAtual])) {
                    $linhasDeCalculo[$indiceLinhaAtual] = array_fill(0, $this->numeroDeColunas, '');
                    $colunasOcupadasNaLinha = []; // Reseta para a nova linha
                }

                // Verifica se a coluna já está ocupada na linha atual
                if (isset($colunasOcupadasNaLinha[$calc['coluna']])) {
                    $indiceLinhaAtual++; // Pula para a próxima linha
                    $linhasDeCalculo[$indiceLinhaAtual] = array_fill(0, $this->numeroDeColunas, '');
                    $colunasOcupadasNaLinha = [];
                }

                // Adiciona o cálculo na posição correta
                $linhasDeCalculo[$indiceLinhaAtual][$calc['coluna']] = htmlspecialchars($calc['label'] . $calc['valor']);
                // Marca a coluna como ocupada para esta linha
                $colunasOcupadasNaLinha[$calc['coluna']] = true;
            }

            // --- RENDERIZAÇÃO DO HTML ---
            foreach ($linhasDeCalculo as $linha) {
                $this->html .= "<tr style='font-weight: bold; background-color: #e9ecef;'>";
                foreach ($linha as $celula) {
                    $this->html .= "<td>" . $celula . "</td>";
                }
                $this->html .= "</tr>";
            }
        }

        // Renderiza as linhas de rodapé padrão (que ocupam toda a largura)
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