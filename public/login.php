<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <?php include 'partials/navbar.php'; ?>
    <div class="container">
        <h1>ログイン</h1>
        <form action="../backend/user_login.php" method="POST">
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" required>

            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">ログイン</button>
        </form>
    </div>
</body>

</html>