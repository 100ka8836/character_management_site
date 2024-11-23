<?php
// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ログインユーザーのキャラクター情報を取得
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM characters WHERE user_id = ?");
$stmt->execute([$user_id]);
$characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

// デコード結果をキャッシュ
foreach ($characters as &$character) {
    $character['characteristics_decoded'] = json_decode($character['characteristics'], true);
    $character['attributes_decoded'] = json_decode($character['attributes'], true);
}
unset($character);

// 基本情報の固定カテゴリ（HP、MP、DBなどを追加）
$fixed_headers = ['職業', '年齢', '性別', 'HP', 'MP', 'DB', 'SAN値(現在)', 'SAN値(最大)', '幸運'];
$characteristics_keys = ['STR', 'CON', 'POW', 'DEX', 'APP', 'SIZ', 'INT', 'EDU'];

// 技能名を収集
$skill_headers = [];
foreach ($characters as $character) {
    $skills = json_decode($character['skills'], true);
    if (is_array($skills)) {
        foreach ($skills as $skill) {
            if (!in_array($skill['name'], $skill_headers)) {
                $skill_headers[] = $skill['name'];
            }
        }
    }
}

// 技能検索用の処理
$search_skill = $_GET['search_skill'] ?? '';
$filtered_skill_headers = $skill_headers;

// 技能名の並び替え
if (!empty($search_skill)) {
    usort($filtered_skill_headers, function ($a, $b) use ($search_skill) {
        $a_pos = stripos($a, $search_skill);
        $b_pos = stripos($b, $search_skill);

        if ($a === $search_skill)
            return -1;
        if ($b === $search_skill)
            return 1;
        if ($a_pos !== false && $b_pos !== false)
            return $a_pos <=> $b_pos;
        if ($a_pos !== false)
            return -1;
        if ($b_pos !== false)
            return 1;

        return 0;
    });
}

// キャラクター情報の取得関数（基本情報用）
function getCharacterValue($character, $header)
{
    $attributes = $character['attributes_decoded'] ?? [];
    return match ($header) {
        '職業' => $character['occupation'] ?? '-',
        '年齢' => $character['age'] ?? '-',
        '性別' => $character['sex'] ?? '-',
        'HP', 'MP', 'DB' => $attributes[strtolower($header)] ?? '-',
        'SAN値(現在)' => $attributes['san']['value'] ?? '-',
        'SAN値(最大)' => $attributes['san']['max'] ?? '-',
        '幸運' => $attributes['luck'] ?? '-',
        default => '-',
    };
}

// 技能値の取得関数
function getSkillValue($character, $skill_name)
{
    $skills = json_decode($character['skills'], true);
    if (is_array($skills)) {
        foreach ($skills as $skill) {
            if ($skill['name'] === $skill_name) {
                return $skill['value'] ?? '-';
            }
        }
    }
    return '-';
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>キャラクター一覧</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab, .tab-content').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');

            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('current_tab', tabId);
            history.replaceState({}, '', `${window.location.pathname}?${urlParams}`);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const currentTab = new URLSearchParams(window.location.search).get('current_tab') || 'tab-basic';
            switchTab(currentTab);
        });
    </script>
</head>

<body>
    <div class="container">

        <!-- アクションボタンをキャラクター一覧の文字の直下に配置 -->
        <div class="action-buttons-container">
            <button id="edit-mode-btn" class="action-button">編集</button>
            <button id="delete-mode-btn" class="action-button">削除</button>
            <button id="update-mode-btn" class="action-button">キャラクター更新</button>
        </div>

        <!-- タブ -->
        <div class="tabs">
            <div class="tab active" data-tab="tab-basic" onclick="switchTab('tab-basic')">基本情報</div>
            <div class="tab" data-tab="tab-characteristics" onclick="switchTab('tab-characteristics')">能力値</div>
            <div class="tab" data-tab="tab-skills" onclick="switchTab('tab-skills')">技能</div>
        </div>

        <!-- 基本情報 -->
        <div id="tab-basic" class="tab-content active">
            <table>
                <thead>
                    <tr>
                        <th>カテゴリ</th>
                        <?php foreach ($characters as $character): ?>
                            <th data-character-id="<?php echo $character['id']; ?>" data-field="name" data-editable="true">
                                <!-- キャラクター名 -->
                                <span id="character-name-<?php echo $character['id']; ?>">
                                    <?php echo htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>


                <tbody>
                    <?php foreach ($fixed_headers as $header): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($header, ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php foreach ($characters as $character): ?>
                                <td data-character-id="<?php echo $character['id']; ?>"
                                    data-field="<?php echo strtolower($header); ?>" data-editable="true">
                                    <?php echo htmlspecialchars(getCharacterValue($character, $header), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>


            </table>
        </div>

        <!-- 能力値 -->
        <div id="tab-characteristics" class="tab-content">
            <table>
                <thead>
                    <tr>
                        <th>カテゴリ</th>
                        <?php foreach ($characters as $character): ?>
                            <th data-character-id="<?php echo $character['id']; ?>">
                                <?php echo htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($characteristics_keys as $key): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php foreach ($characters as $character): ?>
                                <td data-character-id="<?php echo $character['id']; ?>">
                                    <?php echo htmlspecialchars($character['characteristics_decoded'][strtolower($key)] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 技能 -->
        <div id="tab-skills" class="tab-content">
            <!-- 検索フォーム -->
            <form method="GET" class="search-form">
                <input type="hidden" name="current_tab" value="tab-skills">
                <input type="text" name="search_skill" placeholder="技能を検索"
                    value="<?php echo htmlspecialchars($search_skill, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit">検索</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>技能名</th>
                        <?php foreach ($characters as $character): ?>
                            <th data-character-id="<?php echo $character['id']; ?>">
                                <?php echo htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filtered_skill_headers as $skill_name): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($skill_name, ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php foreach ($characters as $character): ?>
                                <td data-character-id="<?php echo $character['id']; ?>">
                                    <?php echo htmlspecialchars(getSkillValue($character, $skill_name), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>