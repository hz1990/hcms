<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\27 0027
 * Time: 9:24
 */
namespace  app\admin\controller;

use think\Controller;
use app\admin\model\Admin;
use think\Session;

class Index extends CheckLogin{
    public function index(){
        return $this->fetch();
    }
    public function main(){
        return $this->fetch();
    }
}