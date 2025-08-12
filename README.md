# Motor de Relatório Dinâmico em PHP

Um motor orientado a objetos em PHP, projetado para gerar relatórios HTML dinâmicos a partir de diversas fontes de dados, como bancos de dados (via PDO) ou APIs. O núcleo do projeto é a classe `Relatorio`, que transforma arrays de dados em tabelas HTML estilizadas, com suporte para cálculos automáticos no rodapé.

## ✨ Principais Características

* **Orientado a Objetos:** Toda a lógica de geração está encapsulada na classe `Relatorio`.
* **Fontes de Dados Flexíveis:**
    * Gera relatórios diretamente de um `PDOStatement`, ideal para bancos de dados SQL.
    * Gera relatórios a partir de arrays PHP simples, permitindo integração com APIs, arquivos CSV, etc.
* **Cálculos no Rodapé:** Inclui uma função para adicionar somas de colunas automaticamente no rodapé do relatório.
* **Estilização via CSS:** O HTML gerado é vinculado a uma folha de estilos (`style.css`) para uma apresentação limpa e profissional.
* **Exemplos Práticos:** O projeto inclui dois exemplos completos para demonstrar seu funcionamento.

---

## 🛠️ Como Usar (Guia para XAMPP)

Olá, Gabriel! Este guia detalhado vai te mostrar como configurar e executar o projeto no seu ambiente XAMPP.

### **Passo 1: Organizar os Arquivos no XAMPP**

1.  **Abra a pasta `htdocs`** do seu XAMPP (geralmente localizada em `C:\xampp\htdocs\`).
2.  Dentro de `htdocs`, crie uma nova pasta para o projeto, por exemplo: `meu-relatorio`.
3.  Copie todos os arquivos do projeto (`Relatorio.php`, `relatorio-via-bd.php`, etc.) para dentro de `C:\xampp\htdocs\meu-relatorio\`.

### **Passo 2: Iniciar o Apache**

1.  Abra o **Painel de Controle do XAMPP**.
2.  Clique em **"Start"** ao lado do módulo **Apache** para iniciar seu servidor web.

### **Passo 3: Rodar o Exemplo com Banco de Dados**

1.  **Crie o Banco de Dados:** Primeiro, precisamos criar o arquivo de banco de dados SQLite. Abra seu navegador e acesse:
    **`http://localhost/meu-relatorio/criar_banco_sqlite.php`**
    *Você só precisa fazer isso uma vez.*

2.  **Gere o Relatório:** Agora, acesse o link para ver o relatório em ação:
    **`http://localhost/meu-relatorio/relatorio-via-bd.php`**

### **Passo 4: Rodar o Exemplo com API (Opcional)**

Este exemplo usa um pequeno servidor em Python para simular uma API.

1.  **Abra um terminal** (CMD ou PowerShell).
2.  **Instale as dependências** do Python com o comando:
    ```bash
    pip install Flask Faker
    ```
3.  **Navegue até a pasta** do projeto no terminal:
    ```bash
    cd C:\xampp\htdocs\meu-relatorio
    ```
4.  **Inicie o servidor da API:**
    ```bash
    python api_servidor_teste.py
    ```
    *Deixe este terminal aberto enquanto testa.*

5.  **Gere o Relatório da API:** Com o servidor Python rodando, acesse no seu navegador:
    **`http://localhost/meu-relatorio/relatorio-via-api.php`**

---

## 📖 A Classe `Relatorio`

O coração do projeto. Seus principais métodos públicos são:

| Método                                                 | Descrição                                                                                             |
| :----------------------------------------------------- | :---------------------------------------------------------------------------------------------------- |
| `__construct()`                                        | Inicializa um objeto de relatório vazio.                                                              |
| `addCalculo(int $coluna, 'soma', string $label)`        | Adiciona uma operação de soma a ser executada em uma coluna específica do relatório.                  |
| `gerarDePdoStatement(string $titulo, PDOStatement $stmt)`| Recebe um `PDOStatement` e monta o relatório completo a partir dele.                                  |
| `init(string $titulo, array $header, array $body, ...)` | O motor principal. Monta o relatório a partir de arrays de cabeçalho, corpo e rodapé.                   |
| `show()`                                               | Imprime o HTML final do relatório diretamente na página.                                              |
| `getHtml()`                                            | Retorna o HTML final do relatório como uma string, para que você possa salvá-lo ou manipulá-lo.       |

---

## 📦 Estrutura do Projeto

```
.
├── Relatorio.php               # (ESSENCIAL) A classe principal do motor de relatórios.
├── relatorio-via-bd.php        # Exemplo de relatório consumindo dados de um BD.
├── relatorio-via-api.php       # Exemplo de relatório consumindo dados de uma API.
├── criar_banco_sqlite.php      # Script auxiliar para criar e popular o banco de teste.
├── style.css                   # Folha de estilos para os relatórios.
└── api_servidor_teste.py       # (OPCIONAL) Servidor em Python/Flask para simular uma API.
```

## 👤 Autor

* **Gabriel Wolff** - [glkwolff](https://github.com/glkwolff)
