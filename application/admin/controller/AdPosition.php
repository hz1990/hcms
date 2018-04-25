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
use think\Loader;
use think\image;

class AdPosition extends CheckLogin{
    public function lst(){
        if(Request::instance()->isGet()){
            $data = db('advert_position')->paginate(20);
            $this->assign('position',$data);
            return $this->fetch();
        }
    }
    public function add()
    {
        if(Request::instance()->isGet()){
            return $this->fetch();
        }else{
            $post = input('post.','','htmlspecialchars');
            $validate = Loader::validate('AdPosition');
            if(!$validate->scene('add')->check($post)){
                $this->error($validate->geterror());
            }
            if(db('advert_position')->insert($post)){
                $this->success('添加成功',url('AdPosition/lst'),'',2000);
            }else{
                $this->error('添加失败');
            }
        }
    }
    public function edit()
    {
        if(Request::instance()->isGet()){
            if($id= Request::instance()->param('id')){
                $position = db('advert_position')->where(['id'=>$id])->find();
                $this->assign('position',$position);
                return $this->fetch();
            }else{
                $this->error('参数错误');
            }
        }else{
            $post = input('post.','','htmlspecialchars');
            $validate = Loader::validate('AdPosition');
            if(!$validate->scene('add')->check($post)){
                $this->error($validate->geterror());
            }
            if(db('advert_position')->update($post)){
                $this->success('修改成功',url('AdPosition/lst'),'',2000);
            }else{
                $this->error('修改失败');
            }
        }
    }
    public function delete()
    {
        if(request()->isGet()){
            if($id= Request::instance()->param('id')){
                if(db('advert')->where(['position_id'=>$id])->find()){
                    return -1;
                }
                if(db('advert_position')->where(['id'=>$id])->delete()){
                    return 1;
                }else{
                    return 0;
                }
            }else{
                return 0;
            }
        }
    }
}