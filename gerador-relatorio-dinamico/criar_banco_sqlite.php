<?php
try {
    // 1. Conexão com o SQLite
    // Se o arquivo não existir, o PHP irá criá-lo automaticamente.
    $pdo = new PDO('sqlite:meu_banco_de_dados.sqlite');
    
    // Habilita a exibição de erros do PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. SQL para criar a tabela 'alunos'
    $sql_create_table = "
    CREATE TABLE IF NOT EXISTS alunos (
      id_aluno INTEGER PRIMARY KEY AUTOINCREMENT,
      nome TEXT NOT NULL,
      email TEXT NOT NULL,
      status TEXT NOT NULL
    )";

    // Executa a criação da tabela
    $pdo->exec($sql_create_table);
    
    // 3. SQL para inserir os dados de exemplo
    // (Apenas insere se a tabela estiver vazia para não duplicar)
    $stmt = $pdo->query("SELECT COUNT(*) FROM alunos");
    if ($stmt->fetchColumn() == 0) {
        $sql_insert_data = "
        INSERT INTO alunos (nome, email, status) VALUES
        ('ANA CAROLINA PEREIRA', 'ana.carolina@email.com', 'Ativo'),
        ('BRUNO DIAS DE ALMEIDA', 'bruno.dias@email.com', 'Ativo'),
        ('CARLOS ANDRADE LIMA', 'carlos.lima@email.com', 'Inativo'),
        ('DANIELA MARTINS ROCHA', 'daniela.rocha@email.com', 'Ativo'),
        ('EDUARDO FERNANDES', 'eduardo.f@email.com', 'Ativo');
        ";
        // Executa a inserção dos dados
        $pdo->exec($sql_insert_data);
        echo "Banco de dados e tabela 'alunos' criados com sucesso! 5 registros inseridos.";
    } else {
        echo "A tabela 'alunos' já contém dados. Nenhuma ação foi necessária.";
    }

} catch (PDOException $e) {
    // Exibe o erro caso algo dê errado
    die("Erro ao conectar ou configurar o banco de dados SQLite: " . $e->getMessage());
}