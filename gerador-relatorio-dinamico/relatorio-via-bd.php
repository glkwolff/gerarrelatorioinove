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
         WHERE status = 'Ativo'
         ORDER BY nome ASC";

// Executa a consulta e obtém o objeto statement
$statement = $pdo->query($sql);

// 3. GERA O RELATÓRIO (A FORMA MAIS SIMPLES POSSÍVEL)
$titulo = "Relatório de Alunos Ativos (Simplificado)";
$relatorio = new Relatorio();

// Colunas: 0=Matrícula, 1=Nome, 2=Status, 3=Valor
$relatorio->addCalculo(3, 'soma', 'Valor Total: R$ ') 
          ->gerarDePdoStatement($titulo, $statement)
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