<?php
/**
 * Created by PhpStorm.
 * User: actionistrator
 * Date: 2017\11\27 0027
 * Time: 9:24
 */
namespace  app\admin\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Db;

class AuthGroup extends CheckLogin{
    public function lst(){
        $data = db('auth_group')->select();
        $this->assign('group',$data);
        return $this->fetch();
    }

    public function add(){
        if(Request::instance()->isGet()){
            $rule=model('AuthRule')->tree();
            $this->assign('rule',$rule);
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $post = input('post.','','htmlspecialchars');
            $data['title']=$post['title'];
            $data['status']=$post['status'];
            $data['rules']=implode(",",$post['rules']);
            $validate = Loader::validate('AuthGroup');
            if(!$validate->check($data)){
                $this->error($validate->geterror());
            }
            $query = db('auth_group')->insert($data);
            if($query){
                $this->success('分组添加成功',url('AuthGroup/lst'),'',2000);
            }else{
                $this->error('分组添加失败');
            }
        }
    }

    public function edit(){
        if(Request::instance()->isGet()){
            if($id= Request::instance()->param('id')){
                $rule=model('AuthRule')->tree();
                $this->assign('rule',$rule);
                $data=db('auth_group')->where(['id'=>$id])->find();
                $rules = explode(",",$data['rules']);
                $this->assign('data',$data);
                $this->assign('rules',$rules);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $post = input('post.','','htmlspecialchars');
            $data['id']=$post['id'];
            $data['title']=$post['title'];
            $data['status']=$post['status'];
            $data['rules']=implode(",",$post['rules']);
            $validate = Loader::validate('AuthGroup');
            if(!$validate->check($data)){
                $this->error($validate->geterror());
            }
            $query = db('auth_group')->update($data);
            if($query){
                $this->success('分组修改成功',url('AuthGroup/lst'),'',2000);
            }else{
                $this->error('分组修改失败');
            }
        }
    }

    public function  delete()
    {
        if (Request::instance()->isGet()) {
            $id = Request::instance()->param('id');
            if (Db::name('admin')->where(['group_id' => $id])->find()) {
                return -1;
            }else {
                if (Db::name('auth_group')->where(['id' => $id])->delete()) {
                    return 1;
                } else {
                    return 0;
                }
            }

        }
    }
}