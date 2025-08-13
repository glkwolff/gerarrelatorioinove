import random
from datetime import datetime, timedelta
from flask import Flask, jsonify, request
from faker import Faker

# --- CONFIGURAÇÃO INICIAL ---
app = Flask(__name__)
# Inicializa o Faker para gerar dados em Português do Brasil
fake = Faker('pt_BR')
dados_alunos_db = []

# --- FUNÇÃO PARA GERAR DADOS ALEATÓRIOS ---
def gerar_dados_alunos(quantidade=100):
    """Gera uma lista de dicionários com dados de alunos aleatórios."""
    lista_alunos = []
    status_opcoes = ['Em Andamento', 'Concluído', 'Cancelado', 'Aguardando Início']
    funcionarios_opcoes = ['THIAGO', 'GABRIEL', 'ANA', 'CARLOS', 'MARIANA']
    matricula_atual = 250

    for _ in range(quantidade):
        nome = fake.name()
        email = fake.email()
        telefone = fake.phone_number()
        # Gera uma data aleatória nos últimos 2 anos
        data_aleatoria = datetime.now() - timedelta(days=random.randint(0, 730))

        aluno = {
            'data': data_aleatoria.strftime('%d/%m/%Y'),
            'matricula': str(matricula_atual),
            'aluno': nome.upper(),
            'email_telefone': f"{email} / {telefone}",
            'endereco': fake.street_address(),
            'bairro': fake.bairro(),
            'cidade_uf': f"{fake.city()} - {fake.estado_sigla()}",
            'status': random.choice(status_opcoes),
            'funcionario': random.choice(funcionarios_opcoes)
        }
        lista_alunos.append(aluno)
        matricula_atual += 1

    # Ordena a lista por data para um resultado mais consistente
    lista_alunos.sort(key=lambda x: datetime.strptime(x['data'], '%d/%m/%Y'))
    return lista_alunos

# --- GERA OS DADOS E ARMAZENA NA MEMÓRIA ---
# Os dados são gerados uma vez quando o servidor é iniciado
dados_alunos_db = gerar_dados_alunos(100)
print(f"-> {len(dados_alunos_db)} registros de alunos gerados com sucesso.")


# --- ROTA DA API (NENHUMA MUDANÇA NA LÓGICA) ---
@app.route('/api/alunos', methods=['GET'])
def get_alunos():
    """
    Filtra os alunos com base nos parâmetros de data da URL.
    """
    data_inicio_str = request.args.get('data_inicio')
    data_fim_str = request.args.get('data_fim')

    # Se não forem fornecidas datas, retorna a lista completa
    if not data_inicio_str or not data_fim_str:
        return jsonify(dados_alunos_db)

    try:
        data_inicio = datetime.strptime(data_inicio_str, '%Y-%m-%d')
        data_fim = datetime.strptime(data_fim_str, '%Y-%m-%d')
    except ValueError:
        return jsonify({"erro": "Formato de data inválido. Use AAAA-MM-DD."}), 400

    alunos_filtrados = []
    for aluno in dados_alunos_db:
        data_aluno = datetime.strptime(aluno['data'], '%d/%m/%Y')
        if data_inicio <= data_aluno <= data_fim:
            alunos_filtrados.append(aluno)

    return jsonify(alunos_filtrados)


# --- INICIA O SERVIDOR ---
if __name__ == '__main__':
    app.run(debug=True, port=5000)