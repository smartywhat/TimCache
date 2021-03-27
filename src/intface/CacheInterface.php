<?php

namespace TimCache\intface;

/**
 * 缓存接口
 */
interface CacheInterface{

    /**
     * 设置缓存
     */
    public function set(String $key, $value, int $time=null, $no_round_time = false);

    /** 
     * 缓存自增加
     */
    public function inc(String $key, int $step=1);

    /** 
     * 缓存自减少
     */
    public function dec(String $key, int $step=1);


    /** 
     * 获取缓存
     */
    public function get(String $key, $defaut=null);

    /** 
     * 删除缓存
     */
    public function rm(String $key);  
    
    /**
     * 获取并删除缓存
     */
    public function pull(String $key);

    /**
     * 清空缓存
     */
    public function clear();

    /*
    * 不存在则写入缓存数据后返回
    */
    public function remember(String $key, Callable $callback);
}