<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\30 0030
 * Time: 10:29
 */

namespace app\admin\validate;
use think\Validate;

class Archives extends  Validate{
    protected $rule =   [
        ['title', 'require', '标题不能为空'],
    ];
}