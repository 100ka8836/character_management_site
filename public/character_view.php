<?php
require '../backend/db.php';
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// キャラクターIDを取得
$character_id = $_GET['id'] ?? null;

if (!$character_id) {
    echo "キャラクターIDが指定されていません。";
    exit();
}

// データベースからキャラクター情報を取得
$stmt = $pdo->prepare("SELECT * FROM characters WHERE id = ? AND user_id = ?");
$stmt->execute([$character_id, $_SESSION['user_id']]);
$character = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$character) {
    echo "キャラクターが見つかりません。";
    exit();
}

// データ表示（上記のコードをここに記載）
$characteristics = json_decode($character['characteristics'], true);
$attribute = json_decode($character['attributes'], true);
$skills = json_decode($character['skills'], true);
$weapons = json_decode($character['weapons'], true);
$possessions = json_decode($character['possessions'], true);
$personalData = json_decode($character['personalData'], true);
$credit = json_decode($character['credit'], true);

$name = htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8');
$occupation = htmlspecialchars($character['occupation'], ENT_QUOTES, 'UTF-8');
$birthplace = htmlspecialchars($character['birthplace'], ENT_QUOTES, 'UTF-8');
$age = htmlspecialchars($character['age'], ENT_QUOTES, 'UTF-8');
$sex = htmlspecialchars($character['sex'], ENT_QUOTES, 'UTF-8');
$portraitURL = htmlspecialchars($character['portrait_url'], ENT_QUOTES, 'UTF-8');
$note = htmlspecialchars($character['note'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>キャラクター情報</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <?php include 'partials/navbar.php'; ?>

    <h1>キャラクター情報</h1>

    <!-- 以下に詳細情報表示コードを記載 -->
    <div class="category">
        <h3>基本情報</h3>
        <ul>
            <li>名前: <?php echo $name; ?></li>
            <li>職業: <?php echo $occupation; ?></li>
            <li>出身: <?php echo $birthplace; ?></li>
            <li>年齢: <?php echo $age; ?></li>
            <li>性別: <?php echo $sex; ?></li>
            <?php if ($portraitURL): ?>
                <li><img src="<?php echo $portraitURL; ?>" alt="プロフィール画像" style="max-width: 150px;"></li>
            <?php endif; ?>
        </ul>
    </div>
    <!-- その他のカテゴリ（上記のカテゴリごとにコードを記載） -->

</body>

</html>