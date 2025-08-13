<?php
// Adicione estas duas linhas no topo para forçar a exibição de erros durante o desenvolvimento
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Inclui a classe Relatorio
require_once 'Relatorio.php';

// --- INÍCIO DA COMUNICAÇÃO COM A API ---

// 2. Define a URL CORRETA da sua API
// ANTES: $apiUrl = 'http://localhost:5000/';
// CORRETO:
$apiUrl = 'http://localhost:5000/api/alunos';


$data_inicio = '2025-01-01'; // Formato AAAA-MM-DD
$data_fim = '2025-03-31';    // Formato AAAA-MM-DD
$apiUrl = "http://localhost:5000/api/alunos?data_inicio={$data_inicio}&data_fim={$data_fim}";



// 3. Usa cURL para fazer a requisição (nenhuma mudança aqui)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$jsonResponse = curl_exec($ch);

// 4. Verifica por erros na comunicação
if (curl_errno($ch)) {
    die('Erro ao se comunicar com a API: ' . curl_error($ch));
}
curl_close($ch);

// --- FIM DA COMUNICAÇÃO COM A API ---

// 5. Decodifica a resposta JSON (nenhuma mudança aqui)
$dadosDaAPI = json_decode($jsonResponse, true);

// Verifica se o JSON foi decodificado corretamente
if ($dadosDaAPI === null) {
    die("Erro ao decodificar o JSON recebido da API. A API pode estar fora do ar ou a URL está incorreta. Resposta recebida: <pre>" . htmlspecialchars($jsonResponse) . "</pre>");
}
if (isset($dadosDaAPI['erro'])) {
    die("A API retornou um erro: " . htmlspecialchars($dadosDaAPI['erro']));
}
if (empty($dadosDaAPI)) {
    die("A API não retornou nenhum dado para os filtros aplicados.");
}


// O restante do código para adaptar os dados e gerar o relatório continua exatamente o mesmo...
// 6. ADAPTAÇÃO DE DADOS
$header = array_keys($dadosDaAPI[0]);
$body = [];
foreach ($dadosDaAPI as $linhaAssociativa) {
    $body[] = array_values($linhaAssociativa);
}

// 7. PREPARAÇÃO DO TÍTULO E RODAPÉ
$titulo = "Relatório de Alunos via API Flask";
$footer = ["Total de registros recebidos: " . count($body)];

// 8. INSTANCIA E GERA O RELATÓRIO
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
        // 9. EXIBE O RELATÓRIO
        $relatorio->show();
    ?>
</body>
</html>