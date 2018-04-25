<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\30 0030
 * Time: 10:29
 */

namespace app\admin\validate;
use think\Validate;

class Model extends  Validate{
    protected $rule =   [
        ['model_name', 'require|min:2|max:30|unique:model', '模型名称不能为空|模型名称不能短于2个字符|模型名称不能超过30个字符|模型名称不能重复'],
        ['table_name', 'require|min:2|max:30|unique:model', '表名不能为空|数据表名称不能短于2个字符|数据表不能超过30个字符|数据表名称不能重复'],
        ['status', 'require|number', '状态必选|状态必须手动选择']
    ];
}