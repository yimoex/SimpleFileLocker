<?php
include 'class.fileLocker.php';

use YimoEx\Packages\fileLocker;

$fl = new fileLocker();
$fl -> setKey('hello');

//加密
$fp = fopen('output_enc.txt', 'a+');
$fl -> encode('file.txt', function($value) use ($fp){
    fwrite($fp, $value);
});
fclose($fp);

//解密
$fp = fopen('output_dec.txt', 'a+');
$fl -> decode('output_enc.txt', function($value) use ($fp){
    fwrite($fp, $value);
});
fclose($fp);