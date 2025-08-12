<?php
require_once 'Relatorio.php';

// 1. CONEXÃO
try {
    $pdo = new PDO('sqlite:meu_banco_de_dados.sqlite');
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}

// 2. CONSULTA SQL
$sql = "SELECT 
            id_aluno AS 'Matrícula',
            nome AS 'Nome do Aluno',
            status AS 'Status',
            valor AS 'Valor'
         FROM alunos 
         ORDER BY nome ASC";

// Executa a consulta
$statement = $pdo->query($sql);

// 3. GERA O RELATÓRIO
$titulo = "Relatório Completo de Alunos";
$relatorio = new Relatorio();

// Colunas: 0=Matrícula, 1=Nome, 2=Status, 3=Valor
$relatorio->addCalculo(3, 'soma', 'Valor Total: R$ ');

// Adiciona os cálculos de porcentagem na coluna 2 (Status)
$relatorio->addPorcentagem(2, 'Ativo', '% Ativos: ');
$relatorio->addPorcentagem(2, 'Inativo', '% Inativos: ');

// Gera e exibe
$relatorio->gerarDePdoStatement($titulo, $statement)
          ->show();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php /* O corpo pode ficar vazio pois o ->show() já foi chamado */ ?>
</body>
</html>