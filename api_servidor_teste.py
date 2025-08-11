from flask import Flask, jsonify, request
from datetime import datetime

# Inicializa o aplicativo Flask
app = Flask(__name__)

# --- DADOS DE EXEMPLO ---
# Usamos os mesmos dados que você tinha no seu arquivo PHP para consistência.
dados_alunos_db = [
    {
        'data': '01/08/2025',
        'matricula': '250',
        'aluno': 'EDNA',
        'email_telefone': 'edna@email.com',
        'endereco': 'Rua A, 123',
        'bairro': 'Centro',
        'cidade_uf': 'São Paulo - SP',
        'status': 'Em Andamento',
        'funcionario': 'THIAGO PRATES SOARES DE MELO'
    },
    {
        'data': '08/08/2025',
        'matricula': '254',
        'aluno': 'GABRIEL LEINEKER WOLFF',
        'email_telefone': 'gabriel@email.com / (41) 98735-8273',
        'endereco': 'RUA LUIZ BOZA 570',
        'bairro': 'BUTIATUVINHA',
        'cidade_uf': 'CURITIBA - PR',
        'status': 'Em Andamento',
        'funcionario': 'GABRIEL'
    },
    {
        'data': '15/08/2025', # Aluno fora do range do teste
        'matricula': '260',
        'aluno': 'MARIA SILVA',
        'email_telefone': 'maria@email.com',
        'endereco': 'Avenida B, 456',
        'bairro': 'Vila Nova',
        'cidade_uf': 'Rio de Janeiro - RJ',
        'status': 'Concluído',
        'funcionario': 'ANA'
    },
]

# --- ROTA DA API ---
# Define o endereço da API, por exemplo: /api/alunos
@app.route('/api/alunos', methods=['GET'])
def get_alunos():
    """
    Esta função será executada quando o PHP acessar a URL.
    Ela filtra os alunos com base nos parâmetros de data da URL.
    """
    # Pega os parâmetros da URL (ex: ?data_inicio=2025-08-01)
    data_inicio_str = request.args.get('data_inicio')
    data_fim_str = request.args.get('data_fim')

    # Se não forem fornecidas datas, retorna todos os alunos
    if not data_inicio_str or not data_fim_str:
        # O jsonify converte o dicionário/lista Python em uma resposta JSON
        return jsonify(dados_alunos_db)

    # Converte as strings de data para objetos de data para comparação
    data_inicio = datetime.strptime(data_inicio_str, '%Y-%m-%d')
    data_fim = datetime.strptime(data_fim_str, '%Y-%m-%d')

    alunos_filtrados = []
    for aluno in dados_alunos_db:
        data_aluno = datetime.strptime(aluno['data'], '%d/%m/%Y')
        if data_inicio <= data_aluno <= data_fim:
            alunos_filtrados.append(aluno)

    # Retorna a lista de alunos filtrados em formato JSON
    return jsonify(alunos_filtrados)


# --- INICIA O SERVIDOR ---
if __name__ == '__main__':
    # O servidor irá rodar no endereço http://127.0.0.1:5000
    app.run(debug=True, port=5000)