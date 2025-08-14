<?php
// A ordem de inclusão é CRÍTICA quando não se usa um autoloader.
// Devemos carregar as dependências ANTES das classes que as utilizam.

// Dependências de AnalisadorDeDados
require_once 'AnalisadorNumerico.php';
require_once 'Calculos.php';

// Classes principais e suas dependências
require_once 'AnalisadorDeDados.php';
require_once 'Filtros.php';
require_once 'GeradorDeRelatorio.php';
require_once 'Renderizadores/GeradorHtml.php';
require_once 'Adaptadores/PdoAdapter.php'; // Dependência do GeradorDeRelatorio

use App\GeradorDeRelatorio;
use App\Renderizadores\GeradorHtml;
use App\Filtros;

// 1. CONEXÃO
try {
    $dbPath = __DIR__ . '/../meu_banco_de_dados.sqlite'; // Caminho para a pasta raiz do projeto
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Erro: " . $e->getMessage()); }

// 2. CONFIGURAÇÃO
$gerador = new GeradorDeRelatorio($pdo); // Corrigido: Agora o PHP sabe onde encontrar a classe
$gerador->setSqlBase("SELECT id_aluno AS 'Matrícula', nome, status, valor FROM alunos");
$gerador->setTitulo("Relatório de Alunos Ativos");

// 3. ADICIONA FILTROS (agora geram SQL WHERE)
$filtros = new Filtros(); // Agora instancia App\Filtros
$filtros->addFiltro('status', '=', 'Ativo');
$filtros->addFiltro('valor', '>', 50);
$gerador->setFiltros($filtros);

// 4. CONFIGURA ANÁLISE
$analisador = $gerador->getAnalisador();
$analisador->addSoma(3, 'Valor Total (Ativos): R$ '); // Coluna 'valor'

// 5. ESCOLHE O RENDERIZADOR E GERA A SAÍDA
$renderizadorHtml = new GeradorHtml(); // Corrigido: Usa a classe importada
$htmlOutput = $gerador->gerar($renderizadorHtml);

// 6. EXIBE O HTML
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($gerador->getTitulo()); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php echo $htmlOutput; ?>
</body>
</html>