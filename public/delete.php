<?php
require_once __DIR__ . '/../db.php';

// 1. URLから id を取得
$id = $_GET['id'] ?? 0;

// 2. DELETE文を使ってデータベースから該当データを削除する
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
    $stmt->execute([$id]);
}

// 3. 削除が終わったら（あるいはidがなくても）一覧画面に戻る
header('Location: index.php');
exit;