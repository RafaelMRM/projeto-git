<?php
header("Content-Type: application/json");
require_once "db.php";

$chat_id = intval($_GET['chat_id'] ?? 0);

if ($chat_id <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT sender, message FROM messages WHERE chat_id = ? ORDER BY id ASC");
$stmt->bind_param("i", $chat_id);
$stmt->execute();

$result = $stmt->get_result();
$mensagens = [];

while ($row = $result->fetch_assoc()) {
    $mensagens[] = $row;
}

echo json_encode($mensagens, JSON_UNESCAPED_UNICODE);
$stmt->close();
?>
