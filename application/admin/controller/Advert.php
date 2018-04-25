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

class Advert extends CheckLogin{
    public function lst(){
        if(Request::instance()->isGet()){
            $data = db('advert')->alias('a')->join('advert_position b','a.position_id = b.id')->field('a.*,b.name')->paginate(20);
            $this->assign('advert',$data);
            return $this->fetch();
        }
    }
    public function add()
    {
        if(Request::instance()->isGet()){
            $position = db('advert_position')->select();
            $this->assign('position',$position);
            return $this->fetch();
        }else{
            $post = input('post.','','htmlspecialchars');
            $data['position_id']=$post['position_id'];
            $data['title']=$post['title'];
            $data['type']=$post['type'];
            $data['status']=$post['status'];
            if(db('advert')->where(['status'=>1,'position_id'=>$data['position_id']])->find() && $data['status']==1){
                db('advert')->where(['position_id'=>$data['position_id'],'status'=>1])->setField('status',0);
            }
            if($post['type'] ==1){
                $data['img_src']=$post['img_src'];
                $data['link']=$post['link'];
                if(db('advert')->insert($data)){
                    $this->success('添加成功',url('advert/lst'),'',2000);
                }else{
                    $this->error('添加失败');
                }
            }else{
                $advert = model('Advert');
                $advert->data($post);
                $add=$advert->save();
                if($add){
                    $this->success('添加成功',url('advert/lst'),'',2000);
                }else{
                    $this->error('添加失败');
                }
            }
        }
    }
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
    public function change_status()
    {
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            $status = db('advert')->where(['id'=>$id])->value('status');
            $position = db('advert')->where(['id'=>$id])->value('position_id');
            if($status ==0){
                if(db('advert')->where(['position_id'=>$position,'status'=>1])->find()){
                    db('advert')->where(['position_id'=>$position,'status'=>1])->setField('status',0);
                }
                db('advert')->where(['id'=>$id])->setField('status',1);
                return 1;
            }else{
                db('advert')->where(['id'=>$id])->setField('status',0);
                return 0;
            }
        }
    }
    public function delete()
    {
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            $type = db('advert')->where(['id'=>$id])->value('type');
            $temp = db('advert')->where(['id'=>$id])->find();
            if($type == 1){
                //image advert delete
                if(file_exists(ROOT_PATH . 'public' .$temp['img_src'])){
                    unlink(ROOT_PATH . 'public' .$temp['img_src']);
                }
                if(db('advert')->where(['id'=>$id])->delete()){
                    return 1;
                }else{
                    return 0;
                }
            }else{
                //flash advert delete
                $img_tmp = db('adflash')->where(['ad_id'=>$id])->select();
                foreach($img_tmp as $key => $val){
                    if(file_exists(ROOT_PATH . 'public' .$val['img_src'])){
                        unlink(ROOT_PATH . 'public' .$val['img_src']);
                    }
                }
                if(db('adflash')->where(['ad_id'=>$id])->delete()){
                    if(db('advert')->where(['id'=>$id])->delete()){
                        return 1;
                    }
                }
                return 0;
            }
        }
    }
    public function edit()
    {
        if(Request::instance()->isGet()){
            if(!$id= Request::instance()->param('id')){
                $this->error('参数错误');
            }
            $position = db('advert_position')->select();
            $this->assign('position',$position);
            $advert = db('advert')->where(['id'=>$id])->find();
            if($advert['type']==2){
                $advert['flash']=db('adflash')->where(['ad_id'=>$id])->select();
            }
            $this->assign('advert',$advert);
            return $this->fetch();
        }else{
            $post = input('post.','','htmlspecialchars');
            $temp = db('advert')->where(['id'=>$post['id']])->find();
            $data['id']=$post['id'];
            $data['position_id']=$post['position_id'];
            $data['title']=$post['title'];
            $data['type']=$post['type'];
            $data['status']=$post['status'];
            if(db('advert')->where(['status'=>1,'position_id'=>$data['position_id']])->find() && $data['status']==1){
                db('advert')->where(['position_id'=>$data['position_id'],'status'=>1])->setField('status',0);
                db('advert')->where(['id'=>$data['id']])->setField('status',1);
            }
            if($post['type'] ==1){
                $data['img_src']=$post['img_src'];
                $data['link']=$post['link'];
                if($temp['type'] !=1){
                    $temp_flash = db('adflash')->where(['ad_id'=>$post['id']])->select();
                    foreach($temp_flash as $key => $val){
                        if(file_exists(ROOT_PATH . 'public' .$val['img_src'])){
                            unlink(ROOT_PATH . 'public' .$val['img_src']);
                        }
                    }
                    db('adflash')->where(['ad_id'=>$post['id']])->delete();
                }
                if(db('advert')->update($data)){
                    $this->success('修改成功',url('advert/lst'),'',2000);
                }else{
                    $this->error('修改失败');
                }
            }else{
                $temp_flash_img=db('adflash')->where(['ad_id'=>$post['id']])->select();
                if($temp['type'] !=2){
                    unlink(ROOT_PATH . 'public' .$temp['img_src']);
                    db('advert')->where(['id'=>$post['id']])->update(['img_src'=>'','link'=>'']);
                }
                if(db('adflash')->where(['ad_id'=>$post['id']])->find()){
                    db('adflash')->where(['ad_id'=>$post['id']])->delete();
                }
                $array=[];
                foreach ($post['flash_src'] as $k => $v){
                    $array[$k]['ad_id']=$post['id'];
                    $array[$k]['img_src']=$v;
                    $array[$k]['link']=$post['flash_link'][$k];
                }
                //删除废弃图片
                if(!empty($temp_flash_img)){
                    foreach ($temp_flash_img as $ke =>$va){
                        if(!in_array($va['img_src'],$post['flash_src'])){
                            if(file_exists(ROOT_PATH . 'public' .$va['img_src'])){
                                unlink(ROOT_PATH . 'public' .$va['img_src']);
                            }
                        }
                    }
                }
                if(db('adflash')->insertAll($array)){
                    $this->success('修改成功',url('advert/lst'),'',2000);
                }else{
                    $this->error('修改失败');
                }
            }
        }
    }
    public function del_flash()
    {
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            $temp = db('adflash')->where(['id'=>$id])->find();
            if(file_exists(ROOT_PATH . 'public' .$temp['img_src'])){
                unlink(ROOT_PATH . 'public' .$temp['img_src']);
            }
            if(db('adflash')->where(['id'=>$id])->delete()){
                return 1;
            }else{
                return 0;
            }
        }
    }
    public function up_flash_img()
    {
        if (Request::instance()->isPost()) {
            foreach ($_FILES as $key => $val) {
                $file = request()->file($key);
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    echo DS . 'uploads' . DS . $info->getSaveName();
                } else {
                    echo $file->getError();
                }
            }
        }
    }
}