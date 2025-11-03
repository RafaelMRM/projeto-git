<?php
header("Content-Type: application/json");
require_once "db.php";

$result = $conn->query("SELECT id, created_at FROM chats ORDER BY created_at DESC");
$chats = [];

while ($row = $result->fetch_assoc()) {
    $chats[] = $row;
}

echo json_encode($chats, JSON_UNESCAPED_UNICODE);
?>
