<?php

// 必要な部品（ファイル）を読み込む
require_once __DIR__ . '/../inc/functions.php';
require_once 'token_check.php';
require_once 'error_check.php';
require_once __DIR__ . '/../inc/db.php';

// CSRFトークンのチェック（不正なPOSTならここで処理が止まる）
checkToken();

// エラーメッセージ用配列
$errors = [];

// POST送信されたときの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // データの入力チェックを実行
    $errors = checkError($_POST);

    // エラーが1つもなければ、データベースに登録
    if (empty($errors)) {
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';

        // INSERT文のプリペアドステートメントを準備する
        $stmt = $pdo->prepare("INSERT INTO items (name, price) VALUES (?, ?)"); // (?, ?)でも(:name, :price)でも同じ
        // 3. execute() を使って安全にデータを流し込んで実行しよう
        $stmt->execute([$name, $price]); // プレースホルダーを使用した場合、$stmt->execute(['name' => $name, 'price' => $price]);と連想配列でデータを渡す必要がある。
        // 4. 登録が終わったら一覧ページにリダイレクトして完了(二重送信防止)
        header('Location: index.php');
        exit;
    }
}

// フォーム埋め込み用のCSRFトークンを取得
$token = setToken();

// items テーブルからすべてのデータを新しい順に取得
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

    <!-- バリデーションエラーがあれば表示する -->
     <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo str2html($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <fieldset>
        <legend>新しい商品を登録する</legend>
        <form method="POST">
            <div>
                <label>商品名: <input type="text" name="name" required></label>
            </div>
            <div>
                <label>価格: <input type="number" name="price" required></label>
                <input type="hidden" name="csrf_token" value="<?php echo str2html($token); ?>">
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
                <td><?php echo str2html($item['id']); ?></td>
                <td><?php echo str2html($item['name']); ?></td>
                <td><?php echo str2html($item['price']); ?></td>
                <td><?php echo str2html($item['created_at']); ?></td>
                <td><a href="edit.php?id=<?php echo $item['id']; ?>">編集</a>
                    <a href="delete.php?id=<?php echo $item['id']; ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                </td>
            </tr>
        <?php endforeach; ?>
</body>
</html>