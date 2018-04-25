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

class AuthRule extends CheckLogin{
    public function lst(){
        $data = model('AuthRule')->tree();
        $this->assign('rule',$data);
        return $this->fetch();
    }

    public function add(){
        if(Request::instance()->isGet()){
            $rule=model('AuthRule')->tree();
            $this->assign('rule',$rule);
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $post = input('post.','','htmlspecialchars');
            $data['name']=$post['name'];
            $data['title']=$post['title'];
            $data['pid']=$post['pid'];
            $data['status']=$post['status'];
            $data['type']=$post['type'];
            $data['condition']=$post['condition'];
            $validate = Loader::validate('AuthRule');
            if(!$validate->check($data)){
                $this->error($validate->geterror());
            }
            $query = db('auth_rule')->insert($data);
            if($query){
                $this->success('规则添加成功',url('AuthRule/lst'),'',2000);
            }else{
                $this->error('规则添加失败');
            }
        }
    }

    public function edit(){
        if(Request::instance()->isGet()){
            if($id= Request::instance()->param('id')){
                $rule=model('AuthRule')->tree();
                $this->assign('rule',$rule);
                $data=db('auth_rule')->where(['id'=>$id])->find();
                $this->assign('data',$data);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $post = input('post.','','htmlspecialchars');
            $data['id']=$post['id'];
            $data['name']=$post['name'];
            $data['title']=$post['title'];
            $data['pid']=$post['pid'];
            $data['status']=$post['status'];
            $data['type']=$post['type'];
            $data['condition']=$post['condition'];
            $validate = Loader::validate('AuthRule');
            if(!$validate->check($data)){
                $this->error($validate->geterror());
            }
            $query = db('auth_rule')->update($data);
            if($query){
                $this->success('规则修改成功',url('AuthRule/lst'),'',2000);
            }else{
                $this->error('规则修改失败');
            }
        }
    }

    public function  delete()
    {
        if (Request::instance()->isGet()) {
            $id = Request::instance()->param('id');
            if (Db::name('auth_rule')->where(['pid' => $id])->find()) {
                return -1;
            }else {
                if (Db::name('auth_rule')->where(['id' => $id])->delete()) {
                    return 1;
                } else {
                    return 0;
                }
            }

        }
    }
}