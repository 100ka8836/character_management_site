<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = $_POST['group_name'];
    $password = $_POST['password'];
    $user_id = $_SESSION['user_id'];

    // バリデーション
    if (empty($group_name) || empty($password)) {
        die("グループ名とパスワードを入力してください。");
    }

    // パスワードをハッシュ化
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // グループを作成
        $stmt = $pdo->prepare("INSERT INTO groups (name, password_hash, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$group_name, $password_hash, $user_id]);
        $group_id = $pdo->lastInsertId();

        // 作成者をグループメンバーとして登録
        $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->execute([$group_id, $user_id]);

        echo "グループが作成されました！";
        echo "<br><a href='group_page.php?group_id=$group_id'>グループページへ</a>";
    } catch (PDOException $e) {
        echo "エラーが発生しました: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php include 'partials/navbar.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>グループ作成</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <h1>新しいグループを作成</h1>
    <form action="" method="POST">
        <label for="group_name">グループ名:</label>
        <input type="text" id="group_name" name="group_name" required>
        <br>
        <label for="password">グループパスワード:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">作成</button>
    </form>
</body>

</html>