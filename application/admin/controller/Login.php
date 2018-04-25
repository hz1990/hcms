<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\29 0029
 * Time: 15:59
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Login extends Controller {
    public function login(){
        if(Request::instance()->isGet()){
            return $this->fetch();
        }elseif (Request::instance()->isPost()){
            //登录处理
            $verify= input('post.verify','','htmlspecialchars');
            if(!captcha_check($verify)){
                $this->error('请输入正确的验证码');
            }  //验证验证码
            if($admin = Db::name('admin')->where(['username'=>$_POST['username']])->find()){
                $password = md5(md5($_POST['password']).$admin['salt']);
                if($password == $admin['password'] ){
                    if($admin['status']!=1){
                        $this->error('该账户不允许登录');
                    }
                    //登录成功
                    Session::set('admin_id',$admin['id']);
                    Session::set('admin_username',$admin['username']);
                    $this->success('登录成功',url('index/index'),'',2000);
                }else{
                    $this->error('密码错误','','',2000);
                }
            }else{
                $this->error('用户不存在');
            }
        }
    }
    public function logout(){
        Session::clear();
        $this->error('请登录',url('login/login'),'',2000);
    }
}