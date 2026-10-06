<?php
// セッションがまだなら開始する（共通処理）
if (!isset($_SESSION)) {
    session_start();
}

// CSRFトークンを発行・取得する関数（フォームに埋め込む用）
function setToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// POST送信された際にトークンが正しいかチェックする関数
function checkToken() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf_token'])) {
            echo "エラーが発生しました";
            exit;
        }
        if (!(hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']))) {
            echo "エラーが発生しました(2)";
            exit;
        }
    }
}
