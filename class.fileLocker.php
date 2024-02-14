<?php
namespace YimoEx\Packages;

class fileLocker {

    public $hash_algo = 'md5';
    public $key = '';

    public $headSize = 64;
    public $buffer = 1024; //读取字节数

    public function setKey($key){
        $this -> key = $key;
    }

    public function setBuffer(int $buf){
        $this -> buffer = $buf;
    }

    public function setAlgo(string $hash_algo){
        $this -> hash_algo = $hash_algo;
    }

    public function setHeadSize(int $headSize){
        $this -> headSize = $headSize;
    }

    public function decode(string $file, \Closure $callback){
        $fp = fopen($file, 'r+');
        $buffer = $this -> buffer;

        $last = $buffer - 1;

        $length = strlen(hash($this -> hash_algo, ''));
        fseek($fp, -$length, SEEK_END);
        $hash = fread($fp, $length);
        fseek($fp, $this -> headSize); //去除混淆头部(详见encode方法)
        $hasher = Hash::create($hash . $this -> key);
        printf("[读取哈希] %s\n", $hash);
        while(!feof($fp)){
            $string = fread($fp, $buffer);
            if(!isset($string[$last])){
                $callback($string);
                continue;
            }
            $t = ord($string[$last]);
            $k = $hasher -> getKey($buffer);
            $string[$last] = chr($t - $k);
            $callback($string);
        }

    }

    public function encode(string $file, \Closure $callback){
        $fp = fopen($file, 'r+');
        $buffer = $this -> buffer;

        $hasher = Hash::create(hash_file($this -> hash_algo, $file));
        $last = $buffer - 1;

        $e = $hasher -> getHash();
        printf("[创建哈希] %s\n", $e);
        $t = $hasher -> getHash() . $this -> key;
        $hash = $hasher -> getHash();
        $hasher = Hash::create(hash($this -> hash_algo, $t));
        $callback($this -> makeRandString($this -> headSize)); //创建混淆头部破坏原来的结构(如mp4等)
        while(!feof($fp)){
            $string = fread($fp, $buffer);
            if(!isset($string[$last])){
                $callback($string);
                continue;
            }
            $t = ord($string[$last]);
            $k = $hasher -> getKey($buffer);
            $string[$last] = chr($k + $t);
            $callback($string);
        }
        $callback($hash);
    }

    public function makeRandString(int $size){
        $res = '';
        for($i = 0;$i < $size;$i++){
            $res .= chr(mt_rand(0, 255));
        }
        return $res;
    }

}

class Hash {

    private $hash;
    private $hashMaps;
    private $length = 0;

    public static function create($hash){
        $k = new Hash();
        $k -> hash = $hash;
        foreach(str_split($hash) as $v){
            $k -> hashMaps[] = ord($v);
        }
        $k -> length = strlen($k -> hash) - 1;
        return $k;
    }

    public function getHash(){
        return $this -> hash;
    }

    public function getKey(int $pos){
        return ($this -> hashMaps)[$pos % $this -> length];
    }

}
