<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\27 0027
 * Time: 9:24
 */
namespace  app\admin\controller;

use think\Controller;
use think\Session;
use think\Request;
use auth\Auth;

class CheckLogin extends Controller{
    public function __construct(){
        parent::__construct();
        if(!Session::get('admin_id')){
            $this->error('请登录',url('login/login'),'',2000);
        }
    }
    public function _initialize()
    {
        if(Session::get('admin_id')){
            //权限验证
            $auth = new Auth();
            $request = Request::instance();
            $module = $request ->module();
            $controller = $request ->controller();
            $action = $request ->action();
            $name = $module.'/'.$controller.'/'.$action;
            if(!$auth ->check($name,Session::get('admin_id'))){
                $this->error('没有权限操作该项',url('admin/index/main'),'',2000);
            }
        }
    }
}