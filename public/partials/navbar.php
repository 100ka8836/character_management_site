<?php
// セッションが開始されていない場合は開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ログイン状態の確認
$isLoggedIn = isset($_SESSION['user_id']);
?>
<nav class="navbar">
    <div class="navbar-container">
        <span class="navbar-brand">キャラクター管理</span> <!-- リンクを削除 -->
        <div class="navbar-links">
            <a href="index.php" class="navbar-link">ホーム</a>
            <a href="character_list.php" class="navbar-link">キャラクター</a>
            <a href="group_create.php" class="navbar-link">グループ作成</a>
            <a href="group_list.php" class="navbar-link">グループ一覧</a>
            <a href="../backend/logout.php" class="navbar-link">ログアウト</a>
        </div>
    </div>
</nav>