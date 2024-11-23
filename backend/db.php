<?php
$host = 'localhost';
$db = 'character_site';
$user = 'root'; // デフォルトユーザー
$pass = ''; // デフォルトではパスワードなし
header('Content-Type: text/html; charset=UTF-8');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8mb4'");


} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}
