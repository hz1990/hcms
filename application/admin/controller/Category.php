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

class Category extends CheckLogin{

    public function lst(){
        $category = model('Category')->tree();
        $this->assign('category',$category);
        return $this->fetch();
    }

    public function add(){
        if(Request::instance()->isGet()){
            //栏目数据
            if($id= Request::instance()->param('id')){
                $parent = db('category')->where(['id'=>$id])->find();
                $this->assign('parent',$parent);
            }
            $cate=model('Category')->tree();
            $this->assign('cate',$cate);
            //模型数据
            $model=db('model')->select();
            $this->assign('model',$model);
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $data=input("post.",'','htmlspecialchars');
            if($data['parent_id'] !=0){
                $parentType = db('category')->where(['id'=>$data['parent_id']])->value('type');
                if($parentType!=1){
                    $this -> error('封面栏目与外部链接不能添加子栏目');
                }
            }
            $validate = Loader::validate('Category');
            if(!$validate->scene('add')->check($data)){
                $this->error($validate->geterror());
            }
            $query=db('category')->insert($data);
            if($query){
                $this -> success('栏目添加成功',url('Category/lst'));
            }else{
                $this -> error('栏目添加失败');
            }
        }
    }

    public function edit(){
        if(Request::instance()->isGet()){
            if($id= Request::instance()->param('id')){
                $cate=model('Category')->tree();
                $this->assign('cate',$cate);
                //模型数据
                $model=db('model')->select();
                $this->assign('model',$model);
                $data=db('category')->where(['id'=>$id])->find();
                $this->assign('data',$data);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $data=input("post.",'','htmlspecialchars');
            $validate = Loader::validate('Category');
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->geterror());
            }
            $query=db('category')->update($data);
            if($query){
                $this -> success('栏目修改成功',url('category/lst'));
            }else{
                $this -> error('栏目修改失败');
            }
        }
    }

    //上传图片
    public function upimg(){
        if(Request::instance()->isPost()){
            foreach($_FILES as $key => $val){
                $file = request()->file($key);
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    echo DS.'uploads'.DS.$info->getSaveName();
                }else{
                    echo $file->getError();
                }
            }

        }
    }

    //切换状态
    public function turn(){
        if(Request::instance()->isGet()){
            $id=input('id',0,'int');
            $status=db('category')->where(['id'=>$id])->field('status')->find();
            if($status['status'] == 1){
                db('category')->where(['id'=>$id])->setField('status',0);
                return 0;
            }else{
                db('category')->where(['id'=>$id])->setField('status',1);
                return 1;
            }
        }
    }

    //排序
    public function sort(){
        $post=input("post.",'','htmlspecialchars');
        $data=$post['sort'];
        foreach($data as $key => $val){
            db('category')->where(['id'=>$key])->setField('sort',$val);
        }
        $this->success('排序成功',url('category/lst'),'',2);
    }

    //单个删除
    public function  delete(){
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            $ids= model('category')->childrenids($id);
            $ids[]=$id;
            foreach ($ids as $val){
                $temp=db('category')->where(['id'=>$val])->find();
                db('category')->where(['id'=>$val])->delete();
                if(!empty($temp['img'])){
                    if(file_exists(ROOT_PATH . 'public'. DS.$temp['img'])){
                        unlink(ROOT_PATH . 'public'. DS.$temp['img']);
                    }
                }
                if(db('archives')->where(['cate_id'=>$val])->find()){
                    $archives = db('archives')->where(['cate_id'=>$val])->select();
                    $table_name = db('model')->where(['id'=>$temp['model_id']])->value('table_name');
                    //删除文档
                    foreach ($archives as $k =>$v){
                        db($table_name)->where(['aid'=>$v['id']])->delete();
                        //删除文档主表及缩略图
                        $litpic = db('archives')->where(['id'=>$v['id']])->value('litpic');
                        if(!empty($litpic) && file_exists(ROOT_PATH . 'public' . DS .$litpic)){
                            unlink(ROOT_PATH . 'public' . DS .$litpic);
                        }
                        db('archives')->where(['id'=>$v['id']])->delete();
                    }
                }
            }
            $this->success('删除成功',url('category/lst'),'',2000);
        }

    }

    //删除选中
    public function del_select(){
        $post=input("post.",'','htmlspecialchars');
        if(!isset($post['del'])){
            $this->error('没有选中任何数据',url('category/lst'),'',2000);
        }
        $data=$post['del'];
        //获取所有需要删除的id
        foreach($data as $key => $val){
            $ids= model('category')->childrenids($key);
            if(!empty($ids)){
                foreach ($ids as $k =>$v){
                    if(!in_array($v,$data)){
                        $data[]=$v;
                    }
                }
            }
        }
        //删除操作
        foreach ($data as $key => $val){
            $temp=db('category')->where(['id'=>$val])->find();
            db('category')->where(['id'=>$val])->delete();
            if(!empty($temp['img'])){
                if(file_exists(ROOT_PATH . 'public'. DS.$temp['img'])){
                    unlink(ROOT_PATH . 'public'. DS.$temp['img']);
                }
            }
            if(db('archives')->where(['cate_id'=>$val])->find()){
                $archives = db('archives')->where(['cate_id'=>$val])->select();
                $table_name = db('model')->where(['id'=>$temp['model_id']])->value('table_name');
                //删除文档
                foreach ($archives as $k =>$v){
                    db($table_name)->where(['aid'=>$v['id']])->delete();
                    //删除文档主表及缩略图
                    $litpic = db('archives')->where(['id'=>$v['id']])->value('litpic');
                    if(!empty($litpic) && file_exists(ROOT_PATH . 'public' . DS .$litpic)){
                        unlink(ROOT_PATH . 'public' . DS .$litpic);
                    }
                    db('archives')->where(['id'=>$v['id']])->delete();
                }
            }
        }
        $this->success('删除成功',url('category/lst'),'',2000);
    }
}