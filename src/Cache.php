<?php

namespace TimCache;

use TimCache\store\Redis;
use TimCache\store\Memory;
use TimCache\store\File;

/**
 * 缓存
 */
class Cache {
    protected static $_instance = [];

    // 进程启动时调用
    public static function init($config)
    {
        if ($config['type'] == 'redis') {
            self::$_instance = new Redis($config);
        } else if ($config['type'] == 'memory') {
            self::$_instance = new Memory($config);
        } else {
            self::$_instance = new File;
        }
    }

    public function __call($name, $arguments)
    {
        return self::$_instance->{$name}(... $arguments);
    }    

    public static function __callStatic($name, $arguments)
    {   
        return self::$_instance->{$name}(... $arguments);
    }
}