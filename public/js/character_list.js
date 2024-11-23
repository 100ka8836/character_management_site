// 編集モードの動作
document.getElementById("edit-mode-btn").addEventListener("click", () => {
  toggleCompleteButton("edit"); // 完了ボタンの生成

  // テーブル内のすべての編集可能な値を対象にする
  document.querySelectorAll("[data-editable]").forEach((editableElement) => {
    const currentValue = editableElement.textContent.trim();
    editableElement.innerHTML = `<input type="text" value="${currentValue}" style="width: 100%;">`;
  });

  // 編集モードボタンを無効化
  document.getElementById("edit-mode-btn").disabled = true;
});

// 編集完了時の処理
function handleEditComplete() {
  const updatedData = [];

  // 入力された値を取得して元の形式に戻す
  document.querySelectorAll("[data-editable]").forEach((editableElement) => {
    const input = editableElement.querySelector("input");
    if (input) {
      const newValue = input.value.trim();
      updatedData.push({
        id: editableElement.dataset.characterId,
        field: editableElement.dataset.field,
        value: newValue
      });
      editableElement.textContent = newValue;
    }
  });

  // 完了ボタンを削除
  removeCompleteButton();

  // サーバーにデータを保存
  saveUpdatedDataToServer(updatedData);

  // 編集モードボタンを再度有効化
  document.getElementById("edit-mode-btn").disabled = false;

  console.log("保存されたデータ:", updatedData);
}

// サーバーにデータを保存する処理
function saveUpdatedDataToServer(updatedData) {
  fetch("../backend/save_character_data.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(updatedData)
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        console.log("データが正常に保存されました。");
      } else {
        console.error("データ保存中にエラーが発生しました:", data.error);
      }
    })
    .catch((error) => {
      console.error("サーバーへの保存中にエラーが発生しました:", error);
    });
}

// 完了ボタンの生成と動作の割り当て
function toggleCompleteButton(mode) {
  const completeButton = document.getElementById("complete-mode-btn");
  if (!completeButton) {
    const button = document.createElement("button");
    button.id = "complete-mode-btn";
    button.textContent = "完了";
    button.className = "action-button";
    document.querySelector(".action-buttons-container").prepend(button);

    if (mode === "edit") {
      button.addEventListener("click", () => handleEditComplete());
    }
  }
}

// 完了ボタンの削除
function removeCompleteButton() {
  const completeButton = document.getElementById("complete-mode-btn");
  if (completeButton) {
    completeButton.remove();
  }
}

// 削除モードの動作
document.getElementById("delete-mode-btn").addEventListener("click", () => {
  toggleCompleteButton("delete"); // 完了ボタンの生成
  document.querySelectorAll("[data-character-id]").forEach((element) => {
    const characterId = element.dataset.characterId;

    // 削除ボタンを追加
    if (!document.getElementById(`delete-btn-${characterId}`)) {
      const deleteButton = document.createElement("button");
      deleteButton.id = `delete-btn-${characterId}`;
      deleteButton.textContent = "削除";
      deleteButton.className = "action-button";
      deleteButton.addEventListener("click", () => handleDelete(characterId));
      element.appendChild(deleteButton);
    }
  });

  // 削除モードボタンを無効化
  document.getElementById("delete-mode-btn").disabled = true;
});

// 削除完了時の処理
function handleDeleteComplete() {
  document
    .querySelectorAll("[id^='delete-btn-']")
    .forEach((btn) => btn.remove());
  removeCompleteButton(); // 完了ボタンを削除
  document.getElementById("delete-mode-btn").disabled = false; // 削除モードボタンを有効化
}

// 全キャラクター更新ボタン
document.getElementById("update-mode-btn").addEventListener("click", () => {
  console.log("全キャラクターを更新します...");
  updateAllCharacters();
});

// キャラクター情報を更新する処理
function updateAllCharacters() {
  fetch("/update_characters.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "update_all" })
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("キャラクター情報が更新されました。");
    })
    .catch((error) =>
      console.error("更新リクエストでエラーが発生しました:", error)
    );
}

// サーバーにデータを保存する処理
function saveUpdatedDataToServer(updatedData) {
  console.log("送信データ:", updatedData);

  fetch("../backend/save_character_data.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(updatedData)
  })
    .then((response) => {
      if (!response.ok) {
        // ステータスコードが 200 以外の場合はエラーテキストを表示
        return response.text().then((text) => {
          throw new Error(`サーバーエラー: ${text}`);
        });
      }
      return response.json();
    })
    .then((data) => {
      console.log("サーバーレスポンス:", data);
      if (data.success) {
        alert("編集内容が保存されました！");
      } else {
        alert("編集内容の保存に失敗しました: " + JSON.stringify(data.error));
      }
    })
    .catch((error) => {
      console.error("サーバーへの保存中にエラーが発生しました:", error);
      alert("サーバーへの保存中にエラーが発生しました。\n" + error.message);
    });
}

// 完了ボタンの生成と動作の割り当て
function toggleCompleteButton(mode) {
  const completeButton = document.getElementById("complete-mode-btn");
  if (!completeButton) {
    const button = document.createElement("button");
    button.id = "complete-mode-btn";
    button.textContent = "完了";
    button.className = "action-button";
    document.querySelector(".action-buttons-container").prepend(button);

    if (mode === "edit") {
      button.addEventListener("click", () => handleEditComplete());
    } else if (mode === "delete") {
      button.addEventListener("click", () => handleDeleteComplete());
    }
  }
}

// 完了ボタンの削除
function removeCompleteButton() {
  const completeButton = document.getElementById("complete-mode-btn");
  if (completeButton) {
    completeButton.remove();
  }
}
