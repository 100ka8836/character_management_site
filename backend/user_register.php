<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームデータを取得
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ユーザー名のバリデーション: 半角英数字のみ、3〜20文字
    if (!preg_match('/^[a-zA-Z0-9]{3,20}$/', $username)) {
        die("ユーザー名は3〜20文字の半角英数字のみ使用できます。");
    }

    // パスワードのバリデーション: 半角英数字のみ、8〜20文字
    if (!preg_match('/^[a-zA-Z0-9]{8,20}$/', $password)) {
        die("パスワードは8〜20文字の半角英数字のみ使用できます。");
    }

    // パスワードをハッシュ化
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // ユーザーをデータベースに登録
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $passwordHash]);

        // 登録成功メッセージ
        echo "ユーザー登録が成功しました！";
        echo "<br><a href='../public/login.php'>ログインページへ</a>";
    } catch (PDOException $e) {
        // エラーメッセージの表示
        if ($e->getCode() === '23000') { // 重複エラー（ユーザー名がユニーク制約に違反）
            echo "このユーザー名は既に登録されています。";
        } else {
            echo "エラーが発生しました: " . $e->getMessage();
        }
    }
}
?>