<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\27 0027
 * Time: 9:24
 */
namespace  app\admin\controller;

use think\Controller;
use think\Request;

class User extends CheckLogin{
    public function role(){
        return $this->fetch();
    }


    public function edit(){
        if(Request::instance()->isGet()){
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            print_r($_POST);
        }
    }
}