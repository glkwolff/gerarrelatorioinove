<?php
require_once 'Relatorio.php';

try {
    // 1. CONEXÃO COM O SQLITE (muito mais simples!)
    $pdo = new PDO('sqlite:meu_banco_de_dados.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Não foi possível conectar ao banco de dados SQLite: " . $e->getMessage());
}

// 2. CONSULTA SQL (a mesma de antes)
$sql = "SELECT 
            id_aluno AS 'Matrícula',
            nome AS 'Nome do Aluno',
            email AS 'E-mail',
            status AS 'Status'
         FROM alunos 
         WHERE status = 'Ativo'
         ORDER BY nome ASC";

$statement = $pdo->query($sql);

// 3. BUSCA E ADAPTAÇÃO DOS DADOS (muito mais fácil com PDO)
// Pega todos os resultados de uma vez em um array associativo
$body_associativo = $statement->fetchAll(PDO::FETCH_ASSOC);

$header = [];
$body = [];

if (!empty($body_associativo)) {
    // Pega o cabeçalho das chaves do primeiro resultado
    $header = array_keys($body_associativo[0]);
    // Converte o array associativo para um array de valores para o corpo
    foreach ($body_associativo as $linha) {
        $body[] = array_values($linha);
    }
}

// 4. Prepara o título e rodapé
$titulo = "Relatório de Alunos Ativos (via SQLite)";
$footer = ["Total de registros encontrados: " . count($body) . " | Gerado em: " . date('d/m/Y')];

// 5. GERA O RELATÓRIO (nenhuma mudança aqui)
$relatorio = new Relatorio();
$relatorio->init($titulo, $header, $body, $footer);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        $relatorio->show();
    ?>
</body>
</html>