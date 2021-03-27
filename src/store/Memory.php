<?php

namespace TimCache\store;

/**
 *  内存缓存 适用于常驻内存程序
 */
class Memory extends Store
{
    protected static $db = null;
    protected static $db_expired_time = null;

    function __construct($config=array())
    {
        self::$db = array();
        self::$db_expired_time = array();
        $this->pexpireTime = isset($config['pexpire_time']) ? $config['pexpire_time'] : $this->pexpireTime;
    }

    /**
     * 设置缓存
     */
    public function set(String $key, $value, int $time=null, $no_round_time = false){
        
        if (empty($key)) return false;

        self::$db[$key] = $value;

        if ($time === null) $time = $this->pexpireTime;

        self::$db_expired_time[$key] = time() + $time;
    }

    /** 
     * 缓存自增加
     */
    public function inc(String $key, int $step=1){
        return self::$db[$key] += $step;
    }

    /** 
     * 缓存自减少
     */
    public function dec(String $key, int $step=1){
        return self::$db[$key] -= $step;
    }

    /** 
     * 获取缓存
     */
    public function get(String $key, $defaut=null){
        if (!isset(self::$db[$key])) return false;

        if (isset(self::$db_expired_time[$key]) && self::$db_expired_time[$key] > 0) {
            if (self::$db_expired_time[$key] < time()) {
                $this->rm($key);
                return false;
            }
        }

        if (rand(1, 10) >= 9) {
            // 删除过期的缓存
            foreach (self::$db_expired_time as $k => $time) {
                if ($time < time()) {
                    unset(self::$db[$k]);
                    unset(self::$db_expired_time[$k]);
                }
            }
        }

        return self::$db[$key];
    }

    /** 
     * 删除缓存
     */
    public function rm(String $key){
        if (isset(self::$db[$key])) unset(self::$db[$key]);
        if (isset(self::$db_expired_time[$key])) unset(self::$db_expired_time[$key]);
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
        self::$db = array();
        self::$db_expired_time = array();
    }

    /*
    * 不存在则写入缓存数据后返回
    */
    public function remember(String $key, Callable $callback){
        $data = $this->get($key);
        if ($data === false) {
            $data = $callback();
            $this->set($key, $data);
        }
        return $data;
    }
}