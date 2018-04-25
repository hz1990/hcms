<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\30 0030
 * Time: 10:29
 */

namespace app\admin\validate;
use think\Validate;

class AdPosition extends  Validate{
    protected $rule =   [
        ['width', 'require', '宽度不能为空'],
        ['height', 'require', '高度不能为空'],
        ['name', 'require|min:2|max:30|unique:advert_position', '广告位名称不能为空|广告位名称不能短于2个字符|广告位名称不能超过30个字符|广告位名称不能重复'],
    ];
}