<?php
require '../backend/db.php';
session_start();

// POSTリクエストでJSONを受信
$input = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['id']) && isset($_SESSION['user_id'])) {
    $character_id = $input['id'];
    $user_id = $_SESSION['user_id'];

    // キャラクターがログインユーザーのものであることを確認しながら削除
    $stmt = $pdo->prepare("DELETE FROM characters WHERE id = ? AND user_id = ?");
    $stmt->execute([$character_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "削除できませんでした。対象が存在しない可能性があります。"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "無効なリクエスト"]);
}
?>