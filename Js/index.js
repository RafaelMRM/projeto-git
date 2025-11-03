let chatId = null;

document.getElementById("enviar").addEventListener("click", async () => {
  const entrada = document.getElementById("entrada").value.trim();
  const chatBox = document.getElementById("chat-box");
  if (!entrada) return;

  // Adiciona mensagem do usuário
  const userMsg = document.createElement("div");
  userMsg.className = "user-message";
  userMsg.innerHTML = `<p>${entrada}</p>`;
  chatBox.appendChild(userMsg);

  // Mensagem temporária
  const botMsg = document.createElement("div");
  botMsg.className = "bot-message";
  botMsg.innerHTML = "<p>Processando mensagem...</p>";
  chatBox.appendChild(botMsg);

  document.getElementById("entrada").value = "";

  // Envia ao PHP
  try {
    const res = await fetch("Back/api.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ texto: entrada, chat_id: chatId })
    });

    const data = await res.json();
    if (data.erro) {
      botMsg.innerHTML = `<p>Erro: ${data.erro}</p>`;
    } else {
      chatId = data.chat_id;
      botMsg.innerHTML = `<p>${data.resposta}</p>`;
    }
  } catch (e) {
    botMsg.innerHTML = "<p>Falha na comunicação com o servidor.</p>";
    console.error(e);
  }

  chatBox.scrollTop = chatBox.scrollHeight;
});
