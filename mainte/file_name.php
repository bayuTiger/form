<?php

$contactFile = '.contact.dat';

//ファイル全読み込み
$fileContents = file_get_contents($contactFile);

// echo $fileContents;

//ファイル書き込み（上書き）
// file_put_contents($contactFile, 'test');

//ファイル書き込み（追記）
file_put_contents($contactFile, 'test2' . "\n",FILE_APPEND);

//csv形式のファイルを読み込む
$allDate = file($contactFile);

foreach($allDate as $lineDate){
  $lines = explode(',', $lineDate);
  echo $lines[0] . '<br>';
  echo $lines[1] . '<br>';
  echo $lines[2] . '<br>';
}
