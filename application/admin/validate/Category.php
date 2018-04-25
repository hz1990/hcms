<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\30 0030
 * Time: 10:29
 */

namespace app\admin\validate;
use think\Validate;

class Category extends  Validate{
    protected $rule =   [
        ['cate_name', 'require|min:2|max:30|unique:category', '栏目名称不能为空|栏目名称不能短于4个字符|栏目名称不能超过30个字符|栏目名称不能重复'],
        ['model_id', 'require|number', '模型id不能为空|模型id必须为整数'],
        ['parent_id', 'require|number', '上级栏目必选|上级栏目id必须为整数']
    ];
    protected $scene=[
        'add'=>['cate_name','model_id','parent_id'],
        'edit'=>['model_id'], //为空则所有生效
    ];
}