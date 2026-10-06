<?php
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/token_check.php';
require_once __DIR__ . '/error_check.php';
require_once __DIR__ . '/../inc/db.php';

// CSRFトークンの検証（POST時のみ動作）
checkToken();

// URLのパラメータから id を取得（数値でなければ一覧にリダイレクト）
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$errors = [];

// 編集する商品のデータを取得
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

// 指定されたIDのデータが存在しない場合
if (!$item) {
    header('Location: index.php');
    exit;
}

//「更新する」ボタンが押されたとき（POST通信）の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 共通のバリデーション関数を使用
    $errors = checkError($_POST);

    // エラーがなければデータベースを更新
    if (empty($errors)) {
        $name = trim($_POST['name']);
        $price = (int)$_POST['price'];

        // UPDATE文を使ってデータを書き換える
        $stmt = $pdo->prepare("UPDATE items SET name = ?, price = ? WHERE id = ?");
        $stmt->execute([$name, $price, $id]);

        // 更新が終わったら一覧画面に戻る
        header('Location: index.php');
        exit;
    }

    // エラーがあった場合はフォームの入力値を保持・更新する
    $item['name'] = $_POST['name'] ?? '';
    $item['price'] = $_POST['price'] ?? '';
}

// CSRFトークンの取得
$token = setToken();
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
    <!-- バリデーションエラーがあれば表示 -->
     <?php if (!empty($errors)): ?>
        <ul class="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo str2html($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label>商品名: <input type="text" name="name" value="<?php echo str2html($item['name']); ?>" required></label>
        </div>
        <div>
            <label>価格: <input type="number" name="price" value="<?php echo str2html($item['price']); ?>" required></label>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo str2html($token); ?>">
        <button type="submit">更新する</button>
        <a href="index.php">キャンセル</a>
    </form>

    <p><a href="index.php">戻る</a></p>
</body>

</html>