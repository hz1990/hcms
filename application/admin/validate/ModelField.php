<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\30 0030
 * Time: 10:29
 */

namespace app\admin\validate;
use think\Validate;

class ModelField extends  Validate{
    protected $rule =   [
        ['field_cname', 'require|min:2|max:30', '字段中文名称不能为空|字段中文名称不能短于2个字符|字段中文名称不能超过30个字符'],
        ['field_ename', 'require|min:2|max:30', '字段英文名称不能为空|字段英文名称不能短于2个字符|字段英文名称不能超过30个字符'],
        ['field_type', 'require|number', '字段类型必选|字段类型必须手动选择'],
        ['model_id', 'require|number', '所属模型必须|模型id必须为整数']
    ];
}