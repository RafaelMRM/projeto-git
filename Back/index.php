<?php
require __DIR__ . '/vendor/autoload.php'; // carrega o autoload do Composer

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__); // define o caminho da raiz do projeto
$dotenv->load(); // carrega as variáveis do .env

// Agora você pode acessar as variáveis assim:
$dbHost = $_ENV['DB_HOST'] ?? 'default_host';
$dbUser = $_ENV['DB_USER'] ?? 'default_user';

echo "Host: $dbHost, Usuário: $dbUser";

header("Content-Type: application/json");

// Lê o corpo da requisição JSON
$data = json_decode(file_get_contents("php://input"), true);
$texto = $data["texto"] ?? "";

// Verifica se veio algo
if (empty($texto)) {
    echo json_encode(["erro" => "Texto vazio"]);
    exit;
}

// Exemplo de requisição para uma API de IA (modelo genérico)
$api_key = " "; // Insira a chave de API aqui
$url = "https://api.openai.com/v1/chat/completions";

$body = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "user", "content" => $texto]
    ]
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $api_key"
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($body)
]);

$response = curl_exec($ch);
curl_close($ch);

// Decodifica a resposta
$resposta = json_decode($response, true);
$mensagem = $resposta["choices"][0]["message"]["content"] ?? "Erro ao gerar resposta.";

echo json_encode(["resposta" => $mensagem]);

?>

