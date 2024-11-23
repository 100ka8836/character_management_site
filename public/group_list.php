<?php
require '../backend/db.php';
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// 自分が参加しているグループを取得
$stmt = $pdo->prepare("
    SELECT g.id, g.name, g.created_by 
    FROM groups g
    JOIN group_members gm ON g.id = gm.group_id
    WHERE gm.user_id = ?
");
$stmt->execute([$user_id]);
$my_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

// グループに参加する処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_name'], $_POST['password'])) {
    $group_name = $_POST['group_name'];
    $password = $_POST['password'];

    // グループ名で検索
    $stmt = $pdo->prepare("SELECT * FROM groups WHERE name = ?");
    $stmt->execute([$group_name]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($group && password_verify($password, $group['password_hash'])) {
        // ユーザーがすでに参加しているか確認
        $stmt = $pdo->prepare("SELECT * FROM group_members WHERE group_id = ? AND user_id = ?");
        $stmt->execute([$group['id'], $user_id]);
        if ($stmt->fetch()) {
            echo "すでにこのグループに参加しています。";
        } else {
            // グループに参加
            $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
            $stmt->execute([$group['id'], $user_id]);
            echo "グループに参加しました！";
            header("Location: group_list.php"); // 再読み込みして更新
            exit();
        }
    } else {
        echo "グループ名またはパスワードが間違っています。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>グループ一覧</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <?php include 'partials/navbar.php'; ?>

    <h1>グループ一覧</h1>

    <h2>参加中のグループ</h2>
    <ul>
        <?php if ($my_groups): ?>
            <?php foreach ($my_groups as $group): ?>
                <li>
                    <?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?>
                    <a href="group_page.php?group_id=<?php echo $group['id']; ?>">グループページへ</a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>現在参加しているグループはありません。</p>
        <?php endif; ?>
    </ul>

    <h2>グループに参加する</h2>
    <form method="POST">
        <label for="group_name">グループ名:</label>
        <input type="text" id="group_name" name="group_name" required>
        <br>
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">参加</button>
    </form>
</body>

</html>