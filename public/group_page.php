<?php
require '../backend/db.php';
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// URLのクエリパラメータからグループIDを取得
if (!isset($_GET['group_id'])) {
    die("グループIDが指定されていません。");
}
$group_id = $_GET['group_id'];
$user_id = $_SESSION['user_id'];

// ユーザーがこのグループに参加しているか確認
$stmt = $pdo->prepare("SELECT * FROM group_members WHERE group_id = ? AND user_id = ?");
$stmt->execute([$group_id, $user_id]);
$is_member = $stmt->fetch();

if (!$is_member) {
    die("このグループにアクセスする権限がありません。");
}

// グループ情報を取得
$stmt = $pdo->prepare("SELECT * FROM groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    die("グループが見つかりません。");
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?> - グループページ</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <!-- ナビゲーションバーを追加 -->
    <?php include 'partials/navbar.php'; ?>

    <h1><?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?> - グループページ</h1>
    <p>ここではこのグループに関する情報や編集機能を利用できます。</p>

    <!-- グループ編集や情報表示のセクション -->
    <section>
        <h2>グループの詳細</h2>
        <p>作成者: <?php echo htmlspecialchars($group['created_by'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p>作成日時: <?php echo htmlspecialchars($group['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
    </section>

    <section>
        <h2>グループのメンバー</h2>
        <ul>
            <?php
            // グループメンバーを取得して表示
            $stmt = $pdo->prepare("
                SELECT u.username 
                FROM users u
                JOIN group_members gm ON u.id = gm.user_id
                WHERE gm.group_id = ?
            ");
            $stmt->execute([$group_id]);
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($members as $member) {
                echo "<li>" . htmlspecialchars($member['username'], ENT_QUOTES, 'UTF-8') . "</li>";
            }
            ?>
        </ul>
    </section>
</body>

</html>