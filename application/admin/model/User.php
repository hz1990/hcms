<?php
namespace app\index\model;
use think\Db;
use think\Model;

class User extends Model{
    //user model
    protected static function getUser($id)
    {
        $data = Db::name('admin')->where(['id'=>$id])->select();
        print_r($data);
    }
}