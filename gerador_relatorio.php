<?php

// --- 1. CONFIGURAÇÃO DAS DATAS ---

// Define um período padrão: últimos 30 dias.
$data_padrao_inicio = date('Y-m-d', strtotime('-30 days'));
$data_padrao_fim = date('Y-m-d'); // Hoje

// Verifica se o formulário foi enviado com novas datas.
// O operador '??' usa o valor da esquerda se ele existir, senão usa o da direita.
$data_inicio = $_GET['data_inicio'] ?? $data_padrao_inicio;
$data_fim = $_GET['data_fim'] ?? $data_padrao_fim;


// --- 2. MONTAGEM DA URL E PREPARAÇÃO DAS VARIÁVEIS ---

// Monta a URL da API dinamicamente com as datas selecionadas.
$url_da_api = "http://127.0.0.1:5000/api/alunos?data_inicio={$data_inicio}&data_fim={$data_fim}";

// Gera um título dinâmico para o relatório.
$titulo_relatorio = "RELATÓRIO DE ALUNOS POR DATA DE MATRÍCULA: "
    . date('d/m/Y', strtotime($data_inicio)) . " ATÉ " . date('d/m/Y', strtotime($data_fim));

$dados_alunos = [];
$mensagem_erro = '';


// --- 3. CHAMADA À API E PROCESSAMENTO ---

$resposta_json = @file_get_contents($url_da_api);

if ($resposta_json !== FALSE) {
    $dados_alunos = json_decode($resposta_json, true);
    // Tratamento de erro caso a API retorne uma mensagem de erro JSON
    if (isset($dados_alunos['erro'])) {
        $mensagem_erro = "Erro da API: " . htmlspecialchars($dados_alunos['erro']);
        $dados_alunos = []; // Limpa os dados para não exibir a tabela
    }
} else {
    // Mensagem de erro se o servidor Python estiver offline
    $mensagem_erro = "ERRO: Não foi possível conectar ao servidor da API. Verifique se o servidor Python (api_servidor_teste.py) está em execução.";
}

$total_alunos = count($dados_alunos);


// --- 4. INCLUI O ARQUIVO DE APRESENTAÇÃO (VIEW) ---
// Todas as variáveis definidas acima ($titulo_relatorio, $dados_alunos, etc.)
// estarão disponíveis dentro do arquivo de template.
include 'relatorio_alunos.html';

?>