<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\30 0030
 * Time: 10:29
 */

namespace app\admin\validate;
use think\Validate;

class AuthRule extends  Validate{
    protected $rule =   [
        ['name', 'require|unique:auth_rule', '规则名称不能为空|规则名称不能重复'],
        ['title', 'require', '密码不能为空'],
        ['status', 'require|number', '状态必选|状态必须手动选择']
    ];
    protected $scene=[
        'add'=>['name,title,status'],
        'edit'=>['name,title,status']
    ];
}