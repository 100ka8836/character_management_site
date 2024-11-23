<?php
session_start();
session_destroy(); // セッションを破棄
header('Location: ../public/login.php'); // ログインページにリダイレクト
exit();
