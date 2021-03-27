### TimCache

#### config

```
[
    'type'=>'memory', 
    'host' => '127.0.0.1', 
    'password'=>'aabbcc', 
    'port'=>6379
]
```


#### demo

```
<?php
require '../vendor/autoload.php';

use TimCache\Cache;


Cache::init(['type'=>'memory', 'host' => '127.0.0.1', 'password'=>'aabbcc', 'port'=>6379]);

Cache::set("aaa", ['aa'=>'cc']);

Cache::set("cc", '1212aaaa');

var_dump(Cache::get("aaa"));

var_dump(Cache::get("cc"));

Cache::set("mynum", 1);
Cache::inc("mynum", 2);

var_dump(Cache::get("mynum"));

var_dump('rm-get');
Cache::rm("mynum");
var_dump(Cache::get("mynum"));

var_dump('put-get');
var_dump(Cache::pull("cc"));
var_dump(Cache::get("cc"));

// Cache::Clear();

Cache::remember("zhonghuaxiaodangjia", function(){
    return '9955113377';
});

var_dump(Cache::get("zhonghuaxiaodangjia"));
```