<?php
/**
 * XSS対策用：特殊文字を安全な文字列に変換する関数
 * @param string $string 変換したい文字列
 * @return string エスケープ後の文字列
 */

function str2html(string $string) :string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

