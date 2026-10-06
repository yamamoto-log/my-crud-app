<?php
/**
 * 送信されたデータのエラーチェックを行う関数
 * @param array $post_data 送信された$_POSTデータ
 * @return array エラーメッセージの配列（エラーがなければ空の配列）
 */

function checkError($post_data) {
    $errors = []; // エラーを貯めるための空の配列

    if (empty($post_data['name'])) {
        $errors[] = "名前を入力してください。";
    }

    if (isset($post_data['content']) && mb_strlen($post_data['content']) > 200) {
        $errors[] = "内容は200文字以内で入力してください。";
    }

    return $errors;
}