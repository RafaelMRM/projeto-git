// script.js

// Adiciona um ouvinte de evento ao botão "Enviar"
// Quando o botão for clicado, executa a função abaixo
document.getElementById("enviar").addEventListener("click", () => {
  
  // Captura o texto digitado pelo usuário no campo de entrada
  const entrada = document.getElementById("entrada").value.trim();
  
  // Captura a div onde será exibida a resposta
  const divResposta = document.getElementById("resposta");
  
  // Verifica se o campo está vazio antes de enviar
  if (entrada === "") {
    divResposta.innerHTML = "<p style='color:red;'>Por favor, digite uma mensagem antes de enviar.</p>";
    return; // Interrompe a execução se o campo estiver vazio
  }

  // Exibe uma mensagem temporária enquanto o servidor processa
  divResposta.innerHTML = "<p><em>Processando mensagem...</em></p>";

  // Faz uma requisição HTTP para o PHP usando o método fetch()
  fetch("backend/ia.php", { // Caminho do arquivo PHP no backend
    method: "POST", // Tipo de envio: POST (para mandar dados)
    headers: { 
      "Content-Type": "application/json" // Informa que os dados estão no formato JSON
    },
    // Converte o texto em JSON e envia ao PHP
    body: JSON.stringify({ texto: entrada })
  })
  // Quando o PHP responder, converte a resposta JSON para objeto JavaScript
  .then(res => res.json())

  // Depois de receber os dados do servidor:
  .then(data => {
    // Se o PHP retornou um erro, mostra ele
    if (data.erro) {
      divResposta.innerHTML = `<p style='color:red;'>Erro: ${data.erro}</p>`;
      return;
    }

    // Exibe a mensagem do usuário e a resposta da IA
    divResposta.innerHTML = `
      <p><strong>Você:</strong> ${entrada}</p>
      <p><strong>IA:</strong> ${data.resposta}</p>
    `;
  })

  // Caso ocorra algum erro na comunicação (ex: servidor offline)
  .catch(err => {
    console.error("Erro de comunicação:", err);
    divResposta.innerHTML = "<p style='color:red;'>Erro ao se comunicar com o servidor.</p>";
  });
});
  