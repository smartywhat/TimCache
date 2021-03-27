<?php
/**
 * phpredis: https://github.com/phpredis/phpredis
 */
namespace TimCache\store;

use TimCache\intface\CacheInterface;

class Redis extends Store
{
    protected static $_instance = null;


    public function __construct($config)
    {
        if (!class_exists("Redis")) {
            throw new Exception("redis is not exists", 1);
        }

        self::$_instance  = new \Redis();

        self::$_instance->connect($config['host'], $config['port']);

        self::$_instance->auth($config['password']);


        if (!self::$_instance->ping()) {
            throw new Exception("can not connect redis", 1);
        }

        $this->prefix      = isset($config['prefix']) ? $config['prefix'] : $this->prefix;
        $this->pexpireTime = isset($config['pexpire_time']) ? $config['pexpire_time'] : $this->pexpireTime;
    }

    /**
     * [serializeKey 返回键序列化标志键名]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function serializeKey($key)
    {
        return $key.'_serialize';
    }

    /**
     * 设置缓存
     */
    public function set(String $key, $value, $time=null, $no_round_time = false){

        if (empty($key)) return false;
        $key = $this->setPrefix($key);

        $has_serialize = 0;
        // 格式化记录
        if (is_array($value) || is_object($value)) {
            $value = serialize($value);
            $has_serialize = 1;
        }

        if ($time === null) $time = $this->pexpireTime;

        if($time > 0) {
            
            // 添加随机时间防止雪崩
            if (!$no_round_time) $time += ($this->pexpireTime + rand(0, 7200));

            self::$_instance->setEx($this->serializeKey($key), $time, $has_serialize);

            return self::$_instance->setEx($key, $time, $value);
        } else {

            self::$_instance->set($this->serializeKey($key), $has_serialize);

            return self::$_instance->set($key, $value);
        }
    }

    /** 
     * 缓存自增加
     */
    public function inc(String $key, $step=1){

        if (empty($key)) return false;

        $key   = $this->setPrefix($key);

        if (is_integer($step)) {
            return self::$_instance->incrBy($key, $step);
        } elseif (is_float($step)) {
            return self::$_instance->incrByFloat($key, $step);
        }
    }

    /** 
     * 缓存自减少
     */
    public function dec(String $key, int $step=1){
        return $this->inc($key, 0-$step);
    }


    /** 
     * 获取缓存
     */
    public function get(String $key, $defaut=false){

        if (empty($key)) return false;

        $key = $this->setPrefix($key);
        
        $has_serialize = self::$_instance->get($this->serializeKey($key));

        $result = self::$_instance->get($key);

        if ($result === false)  return $defaut;

        if ($has_serialize) {
            return unserialize($result);
        } else {
            return $result;
        }
    }

    /** 
     * 删除缓存
     */
    public function rm(String $key){
        $key = $this->setPrefix($key);

        self::$_instance->del($this->serializeKey($key));
        
        return self::$_instance->del($key);
    }  
    
    /**
     * 获取并删除缓存
     */
    public function pull(String $key){
        $data = $this->get($key);
        $this->rm($key);
        return $data;
    }

    /**
     * 清空缓存
     */
    public function clear(){
        $iterator = null;
        while (true) {
            $keys = self::$_instance->scan($iterator, $this->prefix.'*'); 
            if ($keys === false) {
                //迭代结束，未找到匹配pattern的key
                return;
            } 
            self::$_instance->del($keys);
        }
    }

    /*
    * 不存在则写入缓存数据后返回
    */
    public function remember(String $key, Callable $callback){
        if (self::$_instance->exists($this->setPrefix($key))) {
            return $this->get($key);
        } else {
            $data = $callback();
            $this->set($key, $data, 7200);
            return $data;
        }
    }
    
}
