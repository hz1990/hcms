<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'id' => '\d',
    ],
    'test'=>[
       'index/test', ['method' => 'get'],
    ],
    'index'=>[
        'index/Index/index', ['method' => 'get','ext'=>'html'],
    ],
    'cate/:id'=>[
        'index/Cate/index', ['method' => 'get','ext'=>'html'],[':id'=>'/d']
    ],
];
