<?php
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/db.php';

// URLパラメータからIDを取得
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// IDが正しく渡されている場合のみ削除を実行
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
    $stmt->execute([$id]);
}

// 3. 削除が終わったら（あるいはidがなくても）一覧画面に戻る
header('Location: index.php');
exit;
