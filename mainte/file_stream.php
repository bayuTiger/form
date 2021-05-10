<?php

$contactFile = '.contact.dat';

//ファイル名 + モード指定（読み込み専用とか、書き込み専用とか、a+は追記）
//以降これがファイルシステムポインタリソースとして必須の役割を果たす
$contents = fopen($contactFile, 'a+');

$addText = '1行追記' . "\n";

//第一引数にはファイルシステムポインタリソースを記入することに注意！
//ただのファイル名だけじゃ不十分なのです。
fwrite($contents,$addText);

fclose($contactFile);
