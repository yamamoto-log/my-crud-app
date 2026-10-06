<?php
// ルートディレクトリにある .env ファイルから設定値を安全に読み込む処理
// ルートディレクトリにある .env ファイルへのパスを変数 $envPath に入れる
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    // 1. file() 関数を使って .env の中身を丸ごと配列として読み込もう
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // 2. foreach文で1行ずつ取り出そう
    foreach ($lines as $line) {
        // コメント行（#で始まる行）はスキップするおまじない。先頭（位置0）で#が見つかると0を返す
        if (strpos(trim($line), '#') === 0) continue;

        // 3. explode() を使って '=' で分割し、$name と $value に代入しよう
        list($name, $value) = explode('=', $line, 2);

        // スーパーグローバル変数 $_ENV にキレイに格納する
        $_ENV[trim($name)] = trim($value);
    }
}

// 環境変数から値を取得して変数に入れる
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_name = $_ENV['DB_NAME'] ?? 'secure_app_db';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';

try {
    // 1. new PDO() を使ってデータベースに接続しよう
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            // 2. エラーが発生したときに例外（Exception）を投げる設定
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // 3. 取得モードを連想配列（ASSOC）にデフォルト設定
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // プリペアドステートメントのエミュレーションをオフにする（セキュリティ向上）
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // 接続失敗時は安全にエラーメッセージを表示して終了
    exit('データベース接続に失敗しました。') . $e->getMessage();
    // 本番環境では詳しいエラーは表示しない。下記のように表示する。
    // exit('エラー');
}
 ?>