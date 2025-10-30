// script.js

document.addEventListener("DOMContentLoaded", () => {
  
  const botaoEnviar = document.getElementById("enviar");
  const campoEntrada = document.getElementById("entrada");
  const divResposta = document.getElementById("resposta");

  // Função auxiliar para criar e animar uma mensagem no chat
  function criarMensagem(texto, classe) {
    const p = document.createElement("p");
    p.className = `mensagem ${classe}`;
    p.textContent = texto;

    // Adiciona o elemento na div principal
    divResposta.appendChild(p);

    // Força re-renderização (truque para CSS animation)
    void p.offsetWidth;

    // Adiciona a classe que ativa o efeito
    p.classList.add("show");

    // Faz o chat rolar automaticamente para o fim
    divResposta.scrollTop = divResposta.scrollHeight;
  }

  // Enviar mensagem ao clicar no botão
  botaoEnviar.addEventListener("click", () => {
    const mensagem = campoEntrada.value.trim();

    if (mensagem === "") {
      criarMensagem("Por favor, digite algo!", "erro");
      return;
    }

    // Exibe mensagem do usuário
    criarMensagem(`Você: ${mensagem}`, "usuario");
    campoEntrada.value = ""; // limpa o campo

    // Mostra mensagem temporária da IA
    criarMensagem("IA está digitando...", "ia temporario");

    // Envia ao backend
    fetch("backend/ia.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ texto: mensagem })
    })
      .then(res => res.json())
      .then(data => {
        // Remove mensagem "digitando..."
        const temp = document.querySelector(".temporario");
        if (temp) temp.remove();

        if (data.erro) {
          criarMensagem(`Erro: ${data.erro}`, "erro");
        } else {
          criarMensagem(`IA: ${data.resposta}`, "ia");
        }
      })
      .catch(err => {
        const temp = document.querySelector(".temporario");
        if (temp) temp.remove();
        criarMensagem("Erro de conexão com o servidor.", "erro");
        console.error(err);
      });
  });
});

  