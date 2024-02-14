# SimpleFileLocker
由PHP构建的简易的文件加密器



#### 声明

本项目遵守 MIT 协议



#### 优点

* 支持回调函数式写法:

  ```PHP
  (new fileLocker()) -> encode('1.txt', function($value) use ($fp){
      fwrite($fp, $value);
  });
  ```

* 文件中产生的 `哈希` 是原来的文件的哈希值(即验证是否为原来的文件)



#### 可调参数

* (mixed) `Key` (setKey): 设置类全局密钥
* (string) `Algo` (setAlgo): 设置哈希的方法(如MD5, SHA1等)
* (int) `HeadSize` (setHeadSize): 设置混淆头部长度
* (int) `Buffer` (setBuffer): 设置每次读取的长度 **[注: 此项会显著影响性能(值越大加密效果越好,但会延迟加密时间)]**



#### 运行测试(example.php):

```PHP
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
?>
```

