<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

try {
    // デバッグ用：リクエストデータをログに記録
    file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);

    $input = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // 更新対象データが正しいか確認
        if (!is_array($input)) {
            echo json_encode(['success' => false, 'error' => '不正なデータ形式']);
            exit();
        }

        // 更新可能なフィールドのリスト
        $allowed_fields = ['name', 'occupation', 'age', 'sex', 'hp', 'mp', 'db', 'san_value', 'san_max', 'luck'];

        $errors = [];
        foreach ($input as $update) {
            if (isset($update['id'], $update['field'], $update['value'])) {
                $character_id = $update['id'];
                $field = strtolower($update['field']);
                $value = $update['value'];

                // フィールドが許可されたものであるか確認
                if (!in_array($field, $allowed_fields)) {
                    $errors[] = "フィールド '{$field}' は更新できません。";
                    continue;
                }

                // デバッグ用：更新内容をログに記録
                file_put_contents('debug.log', "Updating character ID: {$character_id}, Field: {$field}, Value: {$value}\n", FILE_APPEND);

                // SQLクエリを準備
                $stmt = $pdo->prepare("UPDATE characters SET {$field} = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$value, $character_id, $user_id]);

                if ($stmt->rowCount() === 0) {
                    $errors[] = "キャラクターID {$character_id} のフィールド '{$field}' の更新に失敗しました。";
                }
            } else {
                $errors[] = '更新データに必要な情報が不足しています。';
            }
        }

        if (empty($errors)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $errors]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => '無効なリクエストまたはセッションが無効です。']);
    }
} catch (Exception $e) {
    // 例外をキャッチしてエラー内容をレスポンスとして返す
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
