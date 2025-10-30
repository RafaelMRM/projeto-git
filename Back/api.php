<?php
header("Content-Type: application/json");

// ======================================================
//  CONFIGURAÇÃO DE ERROS — SILENCIOSA E LOGADA
// ======================================================
ini_set('display_errors', 0); // Não exibe erros no navegador
ini_set('log_errors', 1);     // Salva no log
ini_set('error_log', __DIR__ . '/php_error.log'); // Cria log na pasta Back/
error_reporting(E_ALL);       // Captura todos os erros

use Dotenv\Dotenv;

// ======================================================
// CARREGA O AUTOLOAD E AS VARIÁVEIS DE AMBIENTE
// ======================================================
require __DIR__ . '/vendor/autoload.php';

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $api_key = $_ENV['API_KEY'] ?? '';
    $api_url = $_ENV['API_URL'] ?? 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    // ======================================================
    // LÊ E VALIDA A REQUISIÇÃO
    // ======================================================
    $data = json_decode(file_get_contents("php://input"), true);
    $texto = $data["texto"] ?? "";

    if (empty($api_key)) {
        throw new Exception("Erro de configuração: chave de API não encontrada.");
    }

    if (empty($texto)) {
        throw new Exception("Nenhum texto foi enviado.");
    }

    // ======================================================
    // MONTA O CORPO DA REQUISIÇÃO PARA O GEMINI
    // ======================================================
    $body = [
        "contents" => [[
            "role" => "user",
            "parts" => [["text" => $texto]]
        ]]
    ];

    $url_completa = $api_url . "?key=" . $api_key;

    // ======================================================
    // EXECUTA A REQUISIÇÃO COM cURL
    // ======================================================
    $ch = curl_init($url_completa);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_TIMEOUT => 15, // evita travamento
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // ======================================================
    // TRATA ERROS DE CONEXÃO E RESPOSTA
    // ======================================================
    if ($curl_error) {
        throw new Exception("Erro de conexão cURL: $curl_error");
    }

    if ($http_code !== 200) {
        $dados_erro = json_decode($response, true);
        $msg = $dados_erro["error"]["message"] ?? "Erro desconhecido (HTTP $http_code)";
        throw new Exception($msg);
    }

    // ======================================================
    //  PROCESSA A RESPOSTA DA API
    // ======================================================
    $resposta_api = json_decode($response, true);
    $mensagem = $resposta_api["candidates"][0]["content"]["parts"][0]["text"]
        ?? "Não foi possível obter resposta do modelo.";

    echo json_encode([
        "resposta" => $mensagem,
        "texto_original" => $texto
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // ======================================================
    // CAPTURA QUALQUER ERRO E RETORNA JSON LIMPO
    // ======================================================
    error_log("Erro no PR.Chat: " . $e->getMessage());
    echo json_encode(["erro" => $e->getMessage()]);
}