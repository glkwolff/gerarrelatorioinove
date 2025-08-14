<?php

namespace App;

// Com um autoloader PSR-4 configurado, estas chamadas `require_once` não são mais necessárias.
// É recomendado mover as classes AnalisadorDeDados e Filtros para o namespace `App`.

use App\Adaptadores\PdoAdapter;
use App\AnalisadorDeDados; // Adicionado para resolver a classe AnalisadorDeDados
use App\Filtros; // Adicionado para resolver a classe Filtros
use App\Contratos\RenderizadorInterface;
use PDO;
use RuntimeException;

/**
 * Orquestra a criação de um relatório a partir de uma fonte de dados de banco de dados.
 * Constrói a consulta, executa, analisa os dados e os renderiza em um formato específico.
 */
class GeradorDeRelatorio
{
    private PDO $pdo;
    private string $sqlBase = '';
    private Filtros $filtros;
    private AnalisadorDeDados $analisador;
    private string $titulo = "Relatório";

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->analisador = new AnalisadorDeDados();
        $this->filtros = new Filtros();
    }

    /**
     * Define a consulta SQL base para o relatório.
     * @param string $sqlBase A consulta SQL sem a cláusula WHERE.
     * @return self
     */
    public function setSqlBase(string $sqlBase): self
    {
        $this->sqlBase = $sqlBase;
        return $this;
    }

    /**
     * Define o objeto de filtros a ser aplicado na consulta.
     * @param Filtros $filtros
     * @return self
     */
    public function setFiltros(Filtros $filtros): self
    {
        $this->filtros = $filtros;
        return $this;
    }

    /**
     * Retorna a instância do analisador de dados para configuração adicional de cálculos.
     * @return AnalisadorDeDados
     */
    public function getAnalisador(): AnalisadorDeDados
    {
        return $this->analisador;
    }

    /**
     * Retorna o título atual do relatório.
     * @return string
     */
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    /**
     * Define o título do relatório.
     * @param string $titulo
     * @return self
     */
    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;
        return $this;
    }

    /**
     * Executa a consulta com filtros, analisa os dados e renderiza a saída.
     * @param RenderizadorInterface $renderizador A estratégia para renderizar o resultado.
     * @return string O relatório renderizado.
     * @throws RuntimeException Se a consulta SQL base não for definida.
     */
    public function gerar(RenderizadorInterface $renderizador): string
    {
        if (empty($this->sqlBase)) {
            throw new RuntimeException("SQL base não definido.");
        }

        // 1. Gera a cláusula WHERE a partir dos filtros
        $sqlParts = $this->filtros->gerarSqlWhere();
        $sqlFinal = $this->sqlBase . $sqlParts['where'];

        // 2. Prepara e executa a consulta de forma segura
        $statement = $this->pdo->prepare($sqlFinal);
        $statement->execute($sqlParts['params']);

        // 3. Usa o PdoAdapter para padronizar a fonte de dados
        $fonteDeDados = new PdoAdapter($statement);
        $dados = $fonteDeDados->getDados();

        // 4. Analisa os dados
        $dadosAnalisados = $this->analisador->analisar($dados['header'], $dados['body']);
        $dadosAnalisados['titulo'] = $this->titulo;

        // 5. Renderiza usando a estratégia fornecida (HTML, JSON, etc.)
        return $renderizador->render($dadosAnalisados);
    }
}