<div class="registration-form">
    <h2>キャラクター登録</h2>
    <form action="character_list.php" method="POST">
        <!-- サイト種別 -->
        <label for="site_type">サイト:</label>
        <select id="site_type" name="site_type" required>
            <option value="charaeno">キャラエノ</option>
            <option value="others">その他</option>
        </select>

        <!-- キャラクターの所有者 -->
        <label for="owner_type">キャラクター所有者:</label>
        <select id="owner_type" name="owner_type" required>
            <option value="self">自分のキャラクター</option>
            <option value="others">他者のキャラクター</option>
        </select>

        <!-- キャラクター情報のURL -->
        <label for="character_url">キャラクターURL:</label>
        <input type="url" id="character_url" name="character_url" placeholder="https://～～～" required>

        <!-- 登録ボタン -->
        <div class="form-container">
            <button type="submit" class="register-button">登録</button>
        </div>
    </form>
</div>