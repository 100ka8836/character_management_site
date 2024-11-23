<?php
require '../backend/db.php';
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['character_url'])) {
    $user_id = $_SESSION['user_id'];
    $character_url = $_POST['character_url'];
    $site_type = $_POST['site_type'] ?? 'unknown';
    $character_name = $_POST['character_name'] ?? '未設定';

    // キャラクターAPIからデータ取得
    $character_data = fetchCharacterData($character_url);

    if (!$character_data) {
        $_SESSION['error_message'] = "キャラクター情報を取得できませんでした。URLを確認してください。";
        header('Location: character_list.php');
        exit();
    }

    // テーブルに挿入
    $stmt = $pdo->prepare("
        INSERT INTO characters (
            user_id, name, url, occupation, age, sex, birthplace, degree, mental_disorder, 
            characteristics, attributes, skills, weapons, possessions, personal_data, 
            credit, mythos_tomes, artifacts_and_spells, encounters, note, portrait_url, 
            hp, mp, db, san_value, san_max, luck, created_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
        )
    ");

    if (
        $stmt->execute([
            $user_id,
            $character_data['name'] ?? $character_name,
            $character_url,
            $character_data['occupation'] ?? null,
            $character_data['age'] ?? null,
            $character_data['sex'] ?? null,
            $character_data['birthplace'] ?? null,
            $character_data['degree'] ?? null,
            $character_data['mental_disorder'] ?? null,
            json_encode($character_data['characteristics'] ?? []),
            json_encode($character_data['attributes'] ?? []),
            json_encode($character_data['skills'] ?? []),
            json_encode($character_data['weapons'] ?? []),
            json_encode($character_data['possessions'] ?? []),
            json_encode($character_data['personal_data'] ?? []),
            json_encode($character_data['credit'] ?? []),
            $character_data['mythos_tomes'] ?? null,
            $character_data['artifacts_and_spells'] ?? null,
            $character_data['encounters'] ?? null,
            $character_data['note'] ?? null,
            $character_data['portraitURL'] ?? null,
            $character_data['attributes']['hp'] ?? null,
            $character_data['attributes']['mp'] ?? null,
            $character_data['attributes']['db'] ?? null,
            $character_data['attributes']['san']['value'] ?? null,
            $character_data['attributes']['san']['max'] ?? null,
            $character_data['attributes']['luck'] ?? null,
        ])
    ) {
        $_SESSION['success_message'] = "キャラクターが正常に登録されました！";
    } else {
        $_SESSION['error_message'] = "キャラクターの登録に失敗しました。";
    }

    header('Location: character_list.php');
    exit();
}

// キャラクターAPIからデータを取得する関数
function fetchCharacterData($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        return null; // APIリクエスト失敗
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return null; // デコード失敗
    }

    // 必要なデータをフォーマット
    return [
        'name' => $data['name'] ?? '未命名キャラクター',
        'occupation' => $data['occupation'] ?? null,
        'age' => isset($data['age']) ? (string) $data['age'] : null,
        'sex' => $data['sex'] ?? null,
        'birthplace' => $data['birthplace'] ?? null,
        'degree' => $data['degree'] ?? null,
        'mental_disorder' => $data['mentalDisorder'] ?? null,
        'characteristics' => $data['characteristics'] ?? [],
        'attributes' => $data['attribute'] ?? [],
        'skills' => $data['skills'] ?? [],
        'weapons' => $data['weapons'] ?? [],
        'possessions' => $data['possessions'] ?? [],
        'personal_data' => $data['personalData'] ?? [],
        'credit' => $data['credit'] ?? [],
        'mythos_tomes' => $data['mythosTomes'] ?? null,
        'artifacts_and_spells' => $data['artifactsAndSpells'] ?? null,
        'encounters' => $data['encounters'] ?? null,
        'note' => $data['note'] ?? '',
        'portraitURL' => $data['portraitURL'] ?? '',
    ];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>キャラクター</title>
    <link rel="stylesheet" href="css/style.css">$user_id = $_SESSION['user_id'];
    <script>
        // タブ切り替え関数
        function switchTab(tabId) {
            const tabs = document.querySelectorAll('.tab');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
        }
    </script>
</head>

<body>
    <?php include 'partials/navbar.php'; ?>
    <div class="container">

        <!-- キャラクター登録フォーム -->
        <?php include 'partials/character_form.php'; ?>

        <!-- キャラクター一覧 -->
        <?php include 'partials/character_table.php'; ?>
    </div>

    <script src="js/character_list.js"></script>
</body>

</html>