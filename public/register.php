<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <?php include 'partials/navbar.php'; ?>
    <div class="container">
        <h1>ユーザー登録</h1>
        <form action="../backend/user_register.php" method="POST">
            <label for="username">ユーザー名（半角英数字3〜20文字）</label>
            <input type="text" id="username" name="username" pattern="[a-zA-Z0-9]{3,20}" title="半角英数字3〜20文字" required>

            <label for="password">パスワード（半角英数字8〜20文字）</label>
            <input type="password" id="password" name="password" pattern="[a-zA-Z0-9]{8,20}" title="半角英数字8〜20文字"
                required>

            <button type="submit">登録</button>
        </form>
    </div>
</body>

</html>