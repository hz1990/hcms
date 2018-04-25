<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\29 0029
 * Time: 15:19
 */

namespace app\admin\model;
use think\Model;
use think\Session;

class Admin extends Model{
    //登录
    public function login($username,$password){
        $admin = $this->where(['username'=>$username])->find();
        if($admin){
            if($admin['password']==md5(md5($password).$admin['salt'])){
                Session::set('admin_id',$admin['id']);
                Session::set('admin_username',$admin['username']);
                return true;
            }
        }
        return false;
    }

    //登录验证、
    public function check(){
        return Session::get('admin_id');
    }

    //注销
    public function logout(){
        Session::clear();
    }

}