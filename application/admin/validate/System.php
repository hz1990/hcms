<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\30 0030
 * Time: 10:29
 */

namespace app\admin\validate;
use think\Validate;

class System extends  Validate{
    protected $rule =   [
        ['cname', 'require|min:4|max:30|unique:system', '中文名不能为空|中文名不能短于4个字符|中文名不能超过30个字符|中文名不能重复'],
        ['ename', 'require|min:4|max:30|unique:system', '英文名不能为空|英文名不能短于4个字符|中文名不能超过30个字符|英文名不能重复'],
        ['data_type', 'require|number', 'data_type必选|data_type必须位数字'],
        ['config_type', 'require|number', 'data_type必选|config_type必须位数字']
    ];
    protected $scene=[
        'add'=>['cname','ename','data_type','config_type'],
        'edit'=>['ename'], //为空则所有生效
    ];
}