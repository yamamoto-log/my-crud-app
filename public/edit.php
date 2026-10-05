<?php
session_start();
require_once __DIR__ . '/../db.php';

// 1. URLのパラメータから id を取得する（なければ 0）
$id = $_GET['id'] ?? 0;

// 2. 「更新する」ボタンが押されたとき（POST通信）の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';

    if ($name !== '' && $price !== '') {
        // UPDATE文を使ってデータを書き換える
        $stmt = $pdo->prepare("UPDATE items SET name = ?, price = ? WHERE id = ?");
        $stmt->execute([$name, $price, $id]);

        // 更新が終わったら一覧画面に戻る
        header('Location: index.php');
        exit;
    }
}

// 3. 編集する商品のデータを取得
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品編集</title>
</head>

<body>
    <h1>商品の編集</h1>

    <form method="POST">
        <div>
            <label>商品名: <input type="text" name="name" value="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" required></label>
        </div>
        <div>
            <label>価格: <input type="number" name="price" value="<?php echo htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?>" required></label>
        </div>
        <button type="submit">更新する</button>
    </form>

    <p><a href="index.php">戻る</a></p>
</body>

</html>