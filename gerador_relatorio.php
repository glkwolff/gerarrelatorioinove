<?php

// 1. URL DA SUA API DE TESTE LOCAL
// Aponta para o servidor Python que está rodando na sua máquina.
$url_da_api = "http://127.0.0.1:5000/api/alunos?data_inicio=2025-08-01&data_fim=2025-08-11";

// Inicializa o array de dados como vazio por segurança.
$dados_alunos = [];

// 2. FAZ A CHAMADA À API
// O @ suprime erros para que possamos tratá-los de forma controlada.
$resposta_json = @file_get_contents($url_da_api);

// 3. PROCESSA A RESPOSTA
// Apenas processa se a resposta não for falsa (ou seja, se a chamada funcionou).
if ($resposta_json !== FALSE) {
    // Decodifica a string JSON para um array PHP.
    $dados_alunos = json_decode($resposta_json, true);
}

// 4. DEFINE AS VARIÁVEIS PARA O RELATÓRIO
// O título pode ser estático ou gerado dinamicamente.
$titulo_relatorio = "RELATÓRIO DE ALUNOS (via API) POR DATA DE MATRÍCULA: 01/08/2025 ATÉ 11/08/2025.";
$total_alunos = count($dados_alunos);

// 5. INCLUI O TEMPLATE HTML PARA EXIBIR OS DADOS
// Este arquivo não precisa ser alterado.
include 'relatorio_alunos.html';

?>