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
use think\Loader;
use think\Db;

class Admin extends CheckLogin{
    public function lst(){
        $data = db('admin')->paginate(10);
        $this->assign('admin',$data);
        return $this->fetch();
    }

    public function add(){
        if(Request::instance()->isGet()){
            $group = db('auth_group')->select();
            $this->assign('group',$group);
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $post = input('post.','','htmlspecialchars');
            $data['username']=$post['username'];
            $data['password']=$post['password'];
            $data['status']=$post['status'];
            $data['group_id']=$post['group_id'];
            $validate = Loader::validate('admin');
            if(!$validate->scene('add')->check($data)){
                $this->error($validate->geterror());
            }
            //生成标识
            $chars ='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $data['salt']='';
            for ( $i = 0; $i < 6; $i++)
            {
                $data['salt'].= $chars[ mt_rand(0, strlen($chars) - 1) ];
            }
            $data['password'] = md5(md5($data['password']).$data['salt']);
            $query = db('admin')->insertGetId($data);
            if($query){
                $array['uid'] = $query;
                $array['group_id']=$data['group_id'];
                db('auth_group_access')->insert($array);
                $this->success('管理员添加成功',url('admin/lst'),'',2000);
            }else{
                $this->error('管理员添加失败');
            }
        }
    }

    public function edit(){
        if(Request::instance()->isGet()){
            if($id= Request::instance()->param('id')){
                $group = db('auth_group')->select();
                $this->assign('group',$group);
                $data=db('admin')->where(['id'=>$id])->find();
                $this->assign('data',$data);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $post = input('post.','','htmlspecialchars');
            $data['id']=$post['id'];
            $data['username']=$post['username'];
            $data['status']=$post['status'];
            $data['group_id']=$post['group_id'];
            $validate = Loader::validate('admin');
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->geterror());
            }
            if(!empty($post['password'])){
                $data['password']=$post['password'];
                $salt =db('admin')->where(['id'=>$data['id']])->value('salt');
                $data['password'] = md5(md5($data['password']).$salt);
            }
            $query = db('admin')->update($data);
            if($query){
                $array['uid'] = $data['id'];
                $array['group_id']=$data['group_id'];
                db('auth_group_access')->insert($array);
                $this->success('管理员修改成功',url('admin/lst'),'',2000);
            }else{
                $this->error('管理员修改失败');
            }
        }
    }

    public function  delete(){
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            if(db('admin')->where(['id'=>$id])->delete()){
                return 1;
            }else{
                return 0;
            }
        }

    }
}