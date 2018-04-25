<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\30 0030
 * Time: 10:29
 */

namespace app\admin\validate;
use think\Validate;

class AuthGroup extends  Validate{
    protected $rule =   [
        ['title', 'require|unique:auth_group', '用户组名称不能为空|用户组名称不能重复'],
        ['rules', 'require', '权限规则不能为空'],
    ];
}