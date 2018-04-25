<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\30 0030
 * Time: 10:29
 */

namespace app\admin\validate;
use think\Validate;

class Admin extends  Validate{
    protected $rule =   [
        ['username', 'require|min:4|max:20|unique:admin', '管理员名称不能为空|管理员名称不能短于4个字符|管理员名称不能超过20个字符|管理员名称不能重复'],
        ['password', 'require|min:6|max:20', '密码不能为空|密码不能短于6个字符|密码不能超过20个字符'],
        ['status', 'require|number', '状态必选|状态必须手动选择'],
        ['group_id', 'require|number', '分组必选|分组必须手动选择']
    ];
    protected $scene=[
        'add'=>['username,password,status,group_id'],
        'edit'=>['username,status,group_id']
    ];
}