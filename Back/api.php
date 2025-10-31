<?php
header("Content-Type: application/json");

use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

// ======================================================
// CARREGA VARIÁVEIS DE AMBIENTE
// ======================================================
try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad(); // não lança erro se .env não existir

    $api_key = $_ENV['API_KEY'] ?? '';
    $api_url = $_ENV['API_URL'] ?? 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    // ======================================================
    // LÊ E VALIDA A REQUISIÇÃO
    // ======================================================
    $data = json_decode(file_get_contents("php://input"), true);
    $texto = trim($data["texto"] ?? '');

    if (!$api_key) throw new Exception("Chave de API ausente.");
    if (!$texto) throw new Exception("Texto vazio.");

    // ======================================================
    // ENVIA REQUISIÇÃO PARA GEMINI
    // ======================================================
    $body = [
        "contents" => [[
            "role" => "user",
            "parts" => [["text" => $texto]]
        ]]
    ];

    $ch = curl_init($api_url . "?key=" . $api_key);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_TIMEOUT => 15
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) throw new Exception("Erro cURL: $curl_error");
    if ($http_code !== 200) {
        $err = json_decode($response, true);
        $msg = $err["error"]["message"] ?? "Erro HTTP $http_code";
        throw new Exception($msg);
    }

    $res = json_decode($response, true);
    $mensagem = $res["candidates"][0]["content"]["parts"][0]["text"] ?? "Sem resposta.";

    echo json_encode(["resposta" => $mensagem], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    error_log("Erro PR.Chat: " . $e->getMessage());
    echo json_encode(["erro" => $e->getMessage()]);
}


include_once "db.php"; // Conecta ao banco

// Cria um novo chat se não houver um ativo
$chat_id = $_POST["chat_id"] ?? null;

if (!$chat_id) {
    $conn->query("INSERT INTO chats () VALUES ()");
    $chat_id = $conn->insert_id;
}

// Salva a mensagem do usuário
$stmt = $conn->prepare("INSERT INTO messages (chat_id, sender, message) VALUES (?, 'user', ?)");
$stmt->bind_param("is", $chat_id, $texto);
$stmt->execute();

// Salva a resposta do bot
$stmt = $conn->prepare("INSERT INTO messages (chat_id, sender, message) VALUES (?, 'bot', ?)");
$stmt->bind_param("is", $chat_id, $mensagem);
$stmt->execute();

// Retorna resposta e ID do chat (para continuar no mesmo)
echo json_encode([
    "resposta" => $mensagem,
    "chat_id" => $chat_id
]);
