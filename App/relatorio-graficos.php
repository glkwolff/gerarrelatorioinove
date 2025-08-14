<?php
// Carrega as dependências de AnalisadorDeDados
require_once 'AnalisadorNumerico.php';
require_once 'Calculos.php';

require_once 'AnalisadorDeDados.php'; // Agora pode ser carregado
require_once 'Adaptadores/PdoAdapter.php';
require_once 'Renderizadores/GeradorJson.php';

use App\Adaptadores\PdoAdapter;
use App\AnalisadorDeDados;
use App\Renderizadores\GeradorJson;

// 1. CONEXÃO E CONSULTA
$dbPath = __DIR__ . '/../meu_banco_de_dados.sqlite'; // Caminho para a pasta raiz do projeto
$pdo = new PDO('sqlite:' . $dbPath);
$statement = $pdo->query("SELECT status, valor FROM alunos");

// 2. USA O ADAPTADOR PARA OBTER OS DADOS
$fonteDeDados = new PdoAdapter($statement);
$dados = $fonteDeDados->getDados();

// 3. CONFIGURA E EXECUTA A ANÁLISE
$analisador = new AnalisadorDeDados();
$analisador->addPorcentagem(0, 'Ativo', 'Alunos Ativos');
$analisador->addPorcentagem(0, 'Inativo', 'Alunos Inativos');
$analisador->addMedia(1, 'Média Geral de Valor');

$dadosAnalisados = $analisador->analisar($dados['header'], $dados['body']);
$dadosAnalisados['titulo'] = "Dashboard de Alunos";

// 4. USA O RENDERIZADOR JSON PARA PREPARAR OS DADOS PARA O GRÁFICO
$renderizadorJson = new GeradorJson();
$jsonParaGrafico = $renderizadorJson->render($dadosAnalisados);

// 5. EXIBE O HTML com o script que consumirá o JSON
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div style="width: 50%;">
        <canvas id="meuGrafico"></canvas>
    </div>
    <script>
        const dados = <?php echo $jsonParaGrafico; ?>;

        // Lógica para montar o gráfico com os dados do JSON
        const labels = dados.resultados.porcentagens.map(p => p.label);
        const valores = dados.resultados.porcentagens.map(p => p.contagem);

        new Chart(document.getElementById('meuGrafico'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Status de Alunos',
                    data: valores
                }]
            }
        });
    </script>
</body>
</html>