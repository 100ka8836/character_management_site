<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // ユーザーをデータベースで検索
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // パスワードが一致した場合、セッションにユーザー情報を保存
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // トップページへリダイレクト
            header('Location: ../public/index.php');
            exit(); // リダイレクト後にスクリプトを終了
        } else {
            // ユーザー名またはパスワードが間違っている場合
            echo "ユーザー名またはパスワードが間違っています。";
            echo "<br><a href='../public/login.php'>ログインページに戻る</a>";
        }
    } catch (PDOException $e) {
        echo "エラーが発生しました: " . $e->getMessage();
    }
}
?>