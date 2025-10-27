document.getElementById("enviar").addEventListener("click", () => {
    const entrada = document.getElementById("entrada").value;
    
    fetch("backend/ia.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ texto: entrada })
    })
    .then(res => res.json())
    .then(data => {
      document.getElementById("resposta").innerText = data.resposta;
    })
    .catch(err => {
      document.getElementById("resposta").innerText = "Erro: " + err;
    });
  });
  