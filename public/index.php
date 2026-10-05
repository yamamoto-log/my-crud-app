<?php
session_start();



// まだトークンが作られていなければ、ランダムな安全な合言葉を作る
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// データベース接続ファイル（db.php）を読み込む
require_once __DIR__ . '/../db.php';

// 1. POST通信（フォームが送信されたとき）の処理

// セッションがまだなら開始
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION)) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRFトークンのチェック
    if (empty($_POST['csrf_token'])) {
        echo "エラーが発生しました。";
        exit;
    }

    // トークンが一致しないとき（hash_equalsで安全に比較）
    if (!(hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']))) {
        echo "エラーが発生しました。";
        exit;
    }

    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';

    // 簡単なバリデーション（空っぽじゃなければ登録を実行）
    if ($name !== '' && $price !== '') {
        // 2. INSERT文のプリペアドステートメントを準備する
        $stmt = $pdo->prepare("INSERT INTO items (name, price) VALUES (?, ?)"); // (?, ?)でも(:name, :price)でも同じ
        // 3. execute() を使って安全にデータを流し込んで実行しよう
        $stmt->execute([$name, $price]); // プレースホルダーを使用した場合、$stmt->execute(['name' => $name, 'price' => $price]);と連想配列でデータを渡す必要がある。
        // 4. 登録が終わったら一覧ページにリダイレクトして完了(二重送信防止)
        header('Location: index.php');
        exit;
    }
}

// items テーブルからすべてのデータを新しい順に取得する
$stmt = $pdo->query('SELECT * FROM items ORDER BY id DESC');
$items = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品管理アプリ</title>
</head>

<body>
    <h1>商品管理アプリ</h1>
    <fieldset>
        <legend>新しい商品を登録する</legend>
        <form method="POST">
            <div>
                <label>商品名: <input type="text" name="name" required></label>
            </div>
            <div>
                <label>価格: <input type="number" name="price" required></label>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit">登録する</button>
            </div>
        </form>
    </fieldset>

    <h2>商品一覧</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>商品名</th>
            <th>価格</th>
            <th>登録日時</th>
        </tr>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($item['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><a href="edit.php?id=<?php echo $item['id']; ?>">編集</a>
                    <a href="delete.php?id=<?php echo $item['id']; ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                </td>
            </tr>
        <?php endforeach; ?>
</body>

</html>