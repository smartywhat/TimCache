<?php

namespace TimCache\store;

use TimCache\intface\CacheInterface;

/**
 * 
 */
class Store implements CacheInterface
{
    protected $prefix = 'tcache';

    protected $pexpireTime = 7200;

    public function setPrefix($key)
    {
        return $this->prefix ? $this->prefix.'_'.$key : $key;
    }

    /**
     * 设置缓存
     */
    public function set(String $key, $value, int $time=null, $no_round_time = false){
        throw new Exception("method is no exists", 1);
    }

    /** 
     * 缓存自增加
     */
    public function inc(String $key, int $step=1){
        throw new Exception("method is no exists", 1);
    }

    /** 
     * 缓存自减少
     */
    public function dec(String $key, int $step=1){
        throw new Exception("method is no exists", 1);
    }


    /** 
     * 获取缓存
     */
    public function get(String $key, $defaut=null){
        throw new Exception("method is no exists", 1);
    }

    /** 
     * 删除缓存
     */
    public function rm(String $key){
        throw new Exception("method is no exists", 1);
        
    }  
    
    /**
     * 获取并删除缓存
     */
    public function pull(String $key){
        throw new Exception("method is no exists", 1);
        
    }

    /**
     * 清空缓存
     */
    public function clear(){
        throw new Exception("method is no exists", 1);
        
    }

    /*
    * 不存在则写入缓存数据后返回
    */
    public function remember(String $key, Callable $callback){
        throw new Exception("method is no exists", 1);
    }    
}