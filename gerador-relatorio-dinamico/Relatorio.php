<?php

class Relatorio {
    
    protected $html;
    protected $numeroDeColunas = 0;

    public function __construct() {
        $this->html = "";
    }
    
    public function __destruct() {
        
    }
    
    /**
     * Ponto de entrada principal para iniciar a geração do relatório.
     */
    public function init(string $titulo, array $header, array $body, array $footer) {
        $this->setTitulo($titulo);
        
        $this->html .= "<table border='1' cellpadding='5' cellspacing='0' width='100%'>";
        
        $this->setHeaderHeader($header);
        $this->setHeaderBody($body); 
        $this->setHeaderFooter($footer);

        $this->html .= "</table>";
    }
    
    /**
     * Define o título principal do relatório.
     */
    protected function setTitulo(string $titulo) {
        $this->html = "<h1>" . htmlspecialchars($titulo) . "</h1>"; 
    }
    
    /**
     * Monta o cabeçalho da tabela (usa <th> e <thead>).
     */
    protected function setHeaderHeader(array $header) {
        $this->numeroDeColunas = count($header);
        $this->html .= "<thead>"; 
        $this->html .= "<tr>"; 
        foreach ($header as $coluna) {
            $this->html .= "<th>" . htmlspecialchars($coluna) . "</th>"; 
        }
        $this->html .= "</tr>"; 
        $this->html .= "</thead>"; 
    }
    
    /**
     * Monta o corpo da tabela com múltiplas linhas (aceita array de arrays).
     */
    protected function setHeaderBody(array $body) {
        $this->html .= "<tbody>";
        foreach ($body as $linha) {
            $this->html .= "<tr>";
            foreach ($linha as $celula) {
                $this->html .= "<td>" . htmlspecialchars($celula) . "</td>";
            }
            $this->html .= "</tr>"; 
        }
        $this->html .= "</tbody>";
    }
    
    /**
     * Monta o rodapé da tabela (usa <tfoot> e colspan).
     */
    protected function setHeaderFooter(array $footer) {
        if (empty($footer)) {
            return;
        }

        $this->html .= "<tfoot>";
        $this->html .= "<tr>";
        $conteudoFooter = $footer[0] ?? '';
        $this->html .= "<td colspan='" . $this->numeroDeColunas . "' style='text-align:center; font-style:italic;'>" . htmlspecialchars($conteudoFooter) . "</td>";
        $this->html .= "</tr>";
        $this->html .= "</tfoot>";
    }
       
    /**
     * Retorna a string HTML completa do relatório.
     */
    public function getHtml(): string {
        return $this->html;
    }

    /**
     * Imprime o HTML do relatório diretamente na tela.
     */
    public function show(): void {
        echo $this->getHtml();
    }
}