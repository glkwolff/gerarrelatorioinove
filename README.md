# Gerador de Relatórios de Alunos

Este projeto consiste em um sistema simples para gerar relatórios de alunos a partir de uma API de teste. A aplicação é dividida em duas partes: um back-end em Python que serve os dados e um front-end em PHP que exibe os relatórios.

## Tecnologias Utilizadas
* **Front-End:** PHP
* **Back-End (API):** Python com Flask
* **Dados Falsos:** Biblioteca Faker (Python)

---

## Pré-requisitos

Antes de começar, garanta que você tenha os seguintes softwares instalados em sua máquina:

1.  **Ambiente PHP:** É recomendado o uso de um pacote como o [XAMPP](https://www.apachefriends.org/pt_br/index.html) ou [WAMP](https://www.wampserver.com/en/), que já inclui um servidor Apache e PHP.
2.  **Python:** Versão 3.6 ou superior. Você pode baixá-lo em [python.org](https://www.python.org/downloads/).

---

## Instalação e Execução

Siga os passos abaixo para configurar e rodar o projeto.

### 1. Back-End (API em Python)

O servidor Python é responsável por gerar e fornecer os dados dos alunos.

**a) Instale as dependências:**
Abra o terminal ou prompt de comando na pasta do projeto e instale as bibliotecas necessárias:
```bash
pip install Flask Faker
