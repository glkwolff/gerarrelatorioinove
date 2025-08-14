<?php
// HABILITA EXIBIÇÃO DE ERROS PARA DEBUG
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Assistente de Configuração do Banco de Dados</h1>";
echo "<p>Este script irá criar la tabela 'alunos' e populá-la com dados da API de teste (api_servidor_teste.py).</p>";
echo "<p><strong>Atenção:</strong> Certifique-se de que o servidor da API Python está em execução antes de continuar. Veja o README.md para instruções.</p>";
echo "<hr>";

try {
    // 1. Conexão com o SQLite
    $pdo = new PDO('sqlite:meu_banco_de_dados.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. SQL para criar a tabela 'alunos' 
    $sql_create_table = "
    CREATE TABLE IF NOT EXISTS alunos (
      id_aluno INTEGER PRIMARY KEY AUTOINCREMENT,
      nome TEXT NOT NULL,
      email TEXT NOT NULL,
      status TEXT NOT NULL,
      valor NUMERIC NOT NULL
    )";

    $pdo->exec($sql_create_table);
    echo "<p>Tabela 'alunos' verificada/criada com sucesso.</p>";

    // 3. Verifica se a tabela já tem dados
    $stmt = $pdo->query("SELECT COUNT(*) FROM alunos");
    if ($stmt->fetchColumn() > 0) {
        die("<p style='color: green; font-weight: bold;'>A tabela 'alunos' já contém dados. Nenhuma ação foi necessária.</p>");
    }

    // 4. Se a tabela está vazia, busca dados da API
    echo "<p>Tabela 'alunos' está vazia. Tentando buscar dados da API...</p>";
    
    // --- INÍCIO DA COMUNICAÇÃO COM A API ---
    $apiUrl = 'http://localhost:5000/api/alunos';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $jsonResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('Erro ao se comunicar com a API: ' . curl_error($ch) . ". Verifique se o servidor 'api_servidor_teste.py' está rodando.");
    }
    curl_close($ch);
    // --- FIM DA COMUNICAÇÃO COM A API ---

    $dadosDaAPI = json_decode($jsonResponse, true);

    if ($dadosDaAPI === null || !is_array($dadosDaAPI)) {
        throw new Exception("Erro ao decodificar o JSON recebido da API. Resposta recebida: <pre>" . htmlspecialchars($jsonResponse) . "</pre>");
    }
    if (empty($dadosDaAPI)) {
        throw new Exception("A API não retornou nenhum dado.");
    }
    
    echo "<p>Dados recebidos da API com sucesso. Iniciando inserção no banco de dados...</p>";

    // 5. Prepara e executa a inserção dos dados em uma transação
    $pdo->beginTransaction();
    
    $sql_insert = "INSERT INTO alunos (nome, email, status, valor) VALUES (:nome, :email, :status, :valor)";
    $stmt_insert = $pdo->prepare($sql_insert);

    $contador = 0;
    foreach ($dadosDaAPI as $alunoAPI) {
        // Como a API já foi ajustada para fornecer os dados no formato correto,
        // a inserção se torna direta, sem necessidade de mapeamento ou adaptação.
        $stmt_insert->execute([
            ':nome'   => $alunoAPI['nome'],
            ':email'  => $alunoAPI['email'],
            ':status' => $alunoAPI['status'],
            ':valor'  => $alunoAPI['valor']
        ]);
        $contador++;
    }

    $pdo->commit();

    echo "<p style='color: green; font-weight: bold;'>Banco de dados populado com sucesso! {$contador} registros inseridos a partir da API.</p>";

} catch (Exception $e) {
    // Se a transação foi iniciada, faz rollback
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Exibe o erro
    die("<p style='color: red; font-weight: bold;'>ERRO: " . $e->getMessage() . "</p>");
}
