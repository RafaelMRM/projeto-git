// Classes CSS para as mensagens
const botClass = "bot-message";
const userClass = "user-message";

document.getElementById("enviar").addEventListener("click", () => {
    // Captura o texto digitado pelo usuário
    const entrada = document.getElementById("entrada").value.trim();
    const chatBox = document.getElementById("chat-box");

    // Debug: verifica se o JS está capturando a mensagem
    console.log("Mensagem a enviar:", entrada);

    if (!entrada) return; // não envia mensagem vazia

    // Adiciona mensagem do usuário no chat
    const userMsg = document.createElement("div");
    userMsg.className = userClass;
    userMsg.innerHTML = `<p>${entrada}</p>`;
    chatBox.appendChild(userMsg);

    // Limpa o input
    document.getElementById("entrada").value = "";

    // Adiciona mensagem temporária do bot
    const botTemp = document.createElement("div");
    botTemp.className = botClass;
    botTemp.innerHTML = `<p>Processando mensagem...</p>`;
    chatBox.appendChild(botTemp);

    // Envia a mensagem para o PHP
    fetch("Back/api.php", { // <-- aponta para o backend PHP
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ texto: entrada })
    })
    .then(res => res.json())
    .then(data => {
        // Substitui a mensagem temporária pela resposta da IA
        botTemp.innerHTML = `<p>${data.resposta || data.erro}</p>`;
        // Rola para o final do chat
        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(err => {
        botTemp.innerHTML = `<p>Erro ao se comunicar com o servidor.</p>`;
        console.error("Erro de comunicação:", err);
    });
});

let chatId = null;

async function enviarMensagem() {
  const texto = document.getElementById("mensagem").value;
  const resposta = await fetch("../Back/index.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ texto, chat_id: chatId })
  });

  const data = await resposta.json();
  chatId = data.chat_id; // Guarda o ID do chat atual
  exibirMensagem("Você", texto);
  exibirMensagem("Bot", data.resposta);
}
