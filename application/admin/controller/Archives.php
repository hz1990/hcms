<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\27 0027
 * Time: 9:24
 */
namespace  app\admin\controller;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use think\Controller;
use think\Db;
use think\Request;
use think\Loader;
use think\image;

class Archives extends CheckLogin{

    public function lst(){
        if(Request::instance()->isGet()){
            if($cate_id= Request::instance()->param('cate_id')){
                $archives = db('archives')
                    ->alias('a')
                    ->join('category b','a.cate_id = b.id')
                    ->field('a.*,b.cate_name')
                    ->where(['a.cate_id'=>$cate_id])
                    ->paginate(20);
                $this->assign('archives',$archives);
                $this->assign('cate_id',$cate_id);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }

    }

    //添加文档
    public function add(){
        if(Request::instance()->isGet()){
            if($cate_id= Request::instance()->param('cate_id')){
                $array = db('category')->where(['id'=>$cate_id])->find();
                //模型自定义字段
                $model_fields = db('model_field')->where(['model_id'=>$array['model_id']])->select();
                foreach ($model_fields as $key =>$val){
                    $model_fields[$key]['field_value'] = explode(',',$val['field_value']);
                }
                $this->assign('cate',$array);
                $this->assign('model_fields',$model_fields);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $data=input("post.",'','htmlspecialchars');
            $cateType = db('category')->where(['id'=>$data['cate_id']])->value('type');
            if($cateType !=1){
                $this->error('此栏目下不允许添加文档');
            }
            $data['time']=time();
            $validate = Loader::validate('Archives');
            if(!$validate->check($data)){
                $this->error($validate->geterror());
            }
            //文件上传开始
            foreach($_FILES as $key => $val){
                if($_FILES[$key]['size']!=0){
                    $file = request()->file($key);
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                        $data[$key]=DS.'uploads'.DS.$info->getSaveName();
                    }
                }
            }
            //文件上传结束
            $query = Db::query('show columns from hcms_archives');
            $fields =array();
            $array =array();
            $array_model =array();
            foreach ($query as $val){
                $fields[]=$val['Field'];
            }
            foreach ($data as $key =>$val){
                if(is_array($val)){
                    $val=implode(',',$val);
                }
                if(in_array($key,$fields)){
                    $array[$key]=$val;
                }else{
                    $array_model[$key]=$val;
                }
            }
            $query=db('archives')->insert($array);
            if($query){
                $back_id = db('archives')->getLastInsID();
                $array_model['aid']=$back_id;
                //附加表
                $table = db('model')->field('table_name')->where(['id'=>$array['model_id']])->find();
                $table = $table['table_name'];
                if(db($table)->insert($array_model)){
                    //附加表数据添加成功
                    $this -> success('添加成功',url('archives/lst',['cate_id'=>$data['cate_id']]));
                }else{
                    db('archives')->where(['id'=>$back_id])->delete();
                    $this -> error('添加失败');
                }

            }else{
                $this -> error('添加失败');
            }
        }
    }

    //编辑
    public function edit(){
        if(Request::instance()->isGet()){
            if($id= Request::instance()->param('id')){
                $data=db('archives')->where(['id'=>$id])->find();//主表数据
                $model_id = $data['model_id'];
                $table = db('model')->where(['id'=>$model_id])->find();
                $data_field=db($table['table_name'])->where(['aid'=>$id])->find();//主表数据
                $data = array_merge($data,$data_field);
                //print_r($data);
                $this->assign('data',$data);
                $array = db('category')->where(['id'=>$data['cate_id']])->find();
                $model_fields = db('model_field')->where(['model_id'=>$array['model_id']])->select();
                foreach ($model_fields as $key =>$val){
                    $model_fields[$key]['field_value'] = explode(',',$val['field_value']);
                }
                $this->assign('cate',$array);
                $this->assign('model_fields',$model_fields);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $data=input("post.",'','htmlspecialchars');
            $data['time']=time();
            $validate = Loader::validate('Archives');
            if(!$validate->check($data)){
                $this->error($validate->geterror());
            }
            //文件上传开始
            foreach($_FILES as $key => $val){
                if($_FILES[$key]['size']!=0){
                    $file = request()->file($key);
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                        $data[$key]=DS.'uploads'.DS.$info->getSaveName();
                    }
                }
            }
            //文件上传结束
            $query = Db::query('show columns from hcms_archives');
            $fields =array();
            $array =array();
            $array_model =array();
            foreach ($query as $val){
                $fields[]=$val['Field'];
            }
            foreach ($data as $key =>$val){
                if(is_array($val)){
                    $val=implode(',',$val);
                }
                if(in_array($key,$fields)){
                    $array[$key]=$val;
                }else{
                    $array_model[$key]=$val;
                }
            }
            //主表数据修改
            $tmp = db('archives')->where(['id'=>$array['id']])->find();
            $query = db('archives')->update($array);
            //缩略图修改
            if($array['litpic'] != $tmp['litpic']){
                $path = ROOT_PATH . 'public' . DS .$tmp['litpic'];
                if(file_exists($path)){
                    unlink($path);
                }
            }
            //附加表数据修改
            $array_model['aid']=$data['id'];
            $table = db('model')->field('table_name')->where(['id'=>$array['model_id']])->find();
            $table = $table['table_name'];
            $query_s = db($table)->where(['aid'=>$array_model['aid']])->update($array_model);
            if($query || $query_s){
                $this -> success('修改成功',url('archives/lst',['cate_id'=>$data['cate_id']]));
            }else{
                $this -> error('修改失败');
            }
        }
    }

    //上传图片及生成缩略图、水印
    public function upimg(){
        if(Request::instance()->isPost()){
            foreach($_FILES as $key => $val){
                $file = request()->file($key);
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    //生成缩略图、水印
                    $img=\think\Image::open(ROOT_PATH . 'public'.DS.'uploads'.DS.$info->getSaveName());
                    $system = array();
                    $tmp= db('system')->field('ename,value')->select();
                    foreach ($tmp as $key => $val){
                        $system[$val['ename']] = $val['value'];
                    }
                    if($system['water'] =="图片水印"){
                        $img->water(ROOT_PATH . 'public'.$system['waterpicture'],\think\Image::WATER_SOUTHEAST ,50)->save(ROOT_PATH . 'public'.DS.'uploads'.DS.$info->getSaveName());
                    }elseif ($system['water'] =="文字水印"){
                        $img->text($system['waterwords'],'test.ttf',20,'#ffffff')->save(ROOT_PATH . 'public'.DS.'uploads'.DS.$info->getSaveName());
                    }
                    if($system['thumb']=="开启"){
                        $img->thumb(1024, 1024)->save(ROOT_PATH . 'public'.DS.'uploads'.DS.$info->getSaveName());
                    }
                    echo DS.'uploads'.DS.$info->getSaveName();
                }else{
                    echo $file->getError();
                }
            }
        }
    }

    //撤销上传
    public function delimg(){
        if(Request::instance()->isGet()){
            if($path= Request::instance()->param('path')){
                $path =ROOT_PATH . 'public' . DS .$path;
                if(file_exists($path)){
                    if(unlink($path)){
                        return 1;
                    }else{
                        return 0;
                    }
                }
            }else{
                $this->error('参数错误');
            }
        }
    }

    //单个删除
    public function  delete(){
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            $temp=db('archives')->where(['id'=>$id])->find();
            db('archives')->where(['id'=>$id])->delete();
            if(!empty($temp['litpic'])){
                if(file_exists(ROOT_PATH . 'public'. DS.$temp['litpic'])){
                    unlink(ROOT_PATH . 'public'. DS.$temp['litpic']);
                }
            }
            $table = db('model')->where(['id'=>$temp['model_id']])->find();
            $table = $table['table_name'];
            $tmp = db($table)->where(['aid'=>$id])->find();
            db($table)->where(['aid'=>$id])->delete();
            //模型字段
            $model_fields = db("model_field")->where(['model_id'=>$temp['model_id']])->select();
            foreach ($model_fields as $key => $val){
                if($val['field_type'] ==6){
                    if(!empty($tmp[$val['field_ename']])){
                        if(file_exists(ROOT_PATH . 'public'. DS.$tmp[$val['field_ename']])){
                            unlink(ROOT_PATH . 'public'. DS.$tmp[$val['field_ename']]);
                        }
                    }
                }
            }
            $this->success('删除成功',url('archives/lst',['cate_id'=>$temp['cate_id']]));
        }

    }

    //删除选中
    public function del_select(){
        $post=input("post.",'','htmlspecialchars');
        if(!isset($post['del'])){
            $this->error('没有选中任何数据',url('category/lst'),'',2000);
        }
        $data=$post['del'];
        $cate_id =$post['cate_id'];
        //获取所有需要删除的id
        foreach($data as $vo){
            $id= $vo;
            $temp=db('archives')->where(['id'=>$id])->find();
            db('archives')->where(['id'=>$id])->delete();
            if(!empty($temp['litpic'])){
                if(file_exists(ROOT_PATH . 'public'. DS.$temp['litpic'])){
                    unlink(ROOT_PATH . 'public'. DS.$temp['litpic']);
                }
            }
            $table = db('model')->where(['id'=>$temp['model_id']])->find();
            $table = $table['table_name'];
            $tmp = db($table)->where(['aid'=>$id])->find();
            db($table)->where(['aid'=>$id])->delete();
            //模型字段
            $model_fields = db("model_field")->where(['model_id'=>$temp['model_id']])->select();
            foreach ($model_fields as $key => $val){
                if($val['field_type'] ==6){
                    if(!empty($tmp[$val['field_ename']])){
                        if(file_exists(ROOT_PATH . 'public'. DS.$tmp[$val['field_ename']])){
                            unlink(ROOT_PATH . 'public'. DS.$tmp[$val['field_ename']]);
                        }
                    }
                }
            }
        }
        $this->success('删除成功',url('archives/lst',['cate_id'=>$cate_id]));
    }
}