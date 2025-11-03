# ChatBot em PHP

Projeto de chatbot web desenvolvido em PHP, utilizando JavaScript, HTML e CSS para a interface. O sistema roda localmente no XAMPP e armazena as mensagens trocadas em um banco de dados MySQL. O PHP se comunica com uma API de IA via cURL para gerar respostas automáticas.

---

## Tecnologias utilizadas

- PHP — lógica do chatbot e integração com a API de IA  
- JavaScript — comunicação entre front-end e back-end  
- HTML5 — estrutura da interface  
- CSS3 — estilização e layout responsivo  
- MySQL — armazenamento das mensagens do chat  
- XAMPP — servidor local de execução  

---

## Funcionalidades

- Envio e recebimento de mensagens em tempo real  
- Respostas automáticas geradas via API de IA com PHP  
- Registro de conversas no MySQL  
- Interface responsiva e estilizada com CSS  
- Integração direta entre HTML, CSS, JS e PHP  

---

## Estrutura do projeto

PROJETO-GILMAR/
│
├── Back/                         # Lado servidor (PHP)
│   ├── .env                      # Variáveis de ambiente
│   ├── .gitignore                # Arquivos ignorados pelo Git
│   ├── api.php                   # PHP que processa a API e salva os chats
│   ├── composer.json             # Gerenciador de dependências PHP
│   ├── composer.lock             # Lock file do Composer
│   ├── db.php                    # Conexão com o banco de dados
│   ├── history.php               # Retorna histórico de conversas
│   ├── load_chat.php             # Carrega mensagens anteriores
│   └── vendor/                   # Bibliotecas instaladas via Composer
│
├── css/
│   └── styles.css                # Estilos visuais do chat
│
├── Js/
│   └── index.js                  # Comunicação do front-end com o PHP
│
├── Index.html                    # Interface principal do chat
├── Logo PRchat.png               # Logotipo do chatbot
└── Readme.md                     # Documentação do projeto


---

## Banco de dados

**Nome do banco:** `chatbot_db`  
**Tabela:** `messages`

| Campo | Tipo | Descrição |
|-------|------|------------|
| id | INT | Identificador único da mensagem |
| user_message | TEXT | Mensagem enviada pelo usuário |
| bot_reply | TEXT | Resposta gerada pelo bot |
| timestamp | DATETIME | Data e hora da interação |

---

## Como executar o projeto (via XAMPP)

1. Coloque a pasta do projeto dentro do diretório `C:\xampp\htdocs\`  
2. Abra o **Painel de Controle do XAMPP**  
   - Inicie **Apache** e **MySQL**  
3. Acesse o **phpMyAdmin** pelo navegador:
http://localhost/phpmyadmin
4. Crie um banco de dados chamado `chatbot_db`  
5. Crie a tabela `messages` conforme a estrutura acima ou importe um arquivo SQL se houver  
6. Abra a interface no navegador:
http://localhost/projeto-gilmar/projeto-gilmar/Index.html

---

## Funcionamento

1. O usuário digita uma mensagem na interface HTML.  
2. O JavaScript (`index.js`) envia a mensagem para o PHP (`index.php`) via `fetch`/AJAX.  
3. O PHP processa a mensagem e faz a requisição para a API de IA (OpenAI ou similar) usando cURL.  
4. O PHP retorna a resposta da API e a salva no banco MySQL.  
5. O JavaScript exibe a resposta na interface do chat.  

---

## Autores

Rafael Martins
Caio de Souza
Luis Phelipe
Carlos Renan
  
Projeto acadêmico com foco na integração entre front-end e back-end, utilizando PHP, JavaScript e MySQL no ambiente XAMPP.
