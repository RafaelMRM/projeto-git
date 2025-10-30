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


<?php
// Conexão com o MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chatbot_db";

$conn = new mysqli($servername, $username, $password);

// Cria o banco se não existir
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$conn->select_db($dbname);

// Cria as tabelas se não existirem
$conn->query("
CREATE TABLE IF NOT EXISTS chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id INT NOT NULL,
    sender ENUM('user','bot') NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE
)");
?>
