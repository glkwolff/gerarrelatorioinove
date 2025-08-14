<?php
// HABILITA EXIBIÇÃO DE ERROS
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Inclui as novas classes
// Carrega as dependências de AnalisadorDeDados
require_once 'AnalisadorNumerico.php';
require_once 'Calculos.php';

require_once 'AnalisadorDeDados.php'; // Agora pode ser carregado
require_once 'Renderizadores/GeradorHtml.php'; // Caminho corrigido

use App\AnalisadorDeDados;
use App\Renderizadores\GeradorHtml;

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


// 6. ADAPTAÇÃO DE DADOS
// A API retorna um array de objetos, então precisamos extrair o cabeçalho e o corpo
$header = array_keys($dadosDaAPI[0]);
$body = [];
foreach ($dadosDaAPI as $linhaAssociativa) {
    $body[] = array_values($linhaAssociativa);
}

// 7. PREPARAÇÃO DO TÍTULO
$titulo = "Relatório de Alunos via API Flask";

// 8. CONFIGURA O ANALISADOR
$analisador = new AnalisadorDeDados();
// Colunas da API: 0=id, 1=nome, 2=status, 3=valor_mensalidade, 4=data_matricula
$analisador->addSoma(3, 'Total Mensalidades: R$ ');
$analisador->addPorcentagem(2, 'Ativo', '% Ativos: ');
$analisador->addPorcentagem(2, 'Inativo', '% Inativos: ');

// 9. EXECUTA A ANÁLISE
$dadosAnalisados = $analisador->analisar($header, $body);

// 10. GERA E EXIBE O HTML
$geradorHtml = new GeradorHtml(); // Agora instancia App\Renderizadores\GeradorHtml
// O método render() da interface espera um único array.
// O título deve ser adicionado aos dados analisados antes de renderizar.
$dadosAnalisados['titulo'] = $titulo;
$htmlOutput = $geradorHtml->render($dadosAnalisados);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php echo $htmlOutput; ?>
</body>
</html>