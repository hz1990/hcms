<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\27 0027
 * Time: 9:24
 */
namespace  app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Common extends CheckLogin{
    protected $config = array(
        'auth_on'           => true,                      // 认证开关
        'auth_type'         => 2,                         // 认证方式，1为实时认证；2为登录认证。
        'auth_group'        => 'auth_group',        // 用户组数据表名
        'auth_group_access' => 'auth_group_access', // 用户-用户组关系表
        'auth_rule'         => 'auth_rule',         // 权限规则表
        'auth_user'         => 'admin'             // 用户信息表
    );

    public function top(){
        return $this->fetch();
    }
    public function left(){
        $menu = config('menu');
        $this->assign('menu',$menu);
        $group = Db::name($this->config['auth_group_access'])->where(['uid'=>Session::get('admin_id')])->value('group_id');
        $rule = Db::name($this->config['auth_group'])->where(['id'=>$group])->value('rules');
        $rule = explode(',',$rule);
        foreach ($rule as $key =>$val){
            $authRule[]= Db::name('auth_rule')->where(['id'=>$val])->value('name');
        }
        $this ->assign('authRule',$authRule);
        return $this->fetch();
    }
}