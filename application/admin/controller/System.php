<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\27 0027
 * Time: 9:24
 */
namespace  app\admin\controller;

use think\Request;
use think\Loader;

class System extends CheckLogin{

    public function index(){
        if(Request::instance()->isGet()){
            $system = db('system')->select();
            foreach($system as $key => $val){
                if($val['values']){
                    $system[$key]['values']=explode(',',$val['values']);
                }
                if(strstr($val['value'],",")){
                    $system[$key]['value']=explode(',',$val['value']);
                }
            }
            $this->assign('system',$system);
            return $this->fetch();
        }elseif (Request::instance()->isPost()){
            $data=input("post.",'','htmlspecialchars');
            $tmp = db('system')->where(['config_type'=>$data['config_type']])->column('ename');
            //附件类型数据处理
            if(!empty($_FILES)){
                foreach($_FILES as $key => $val){
                    //upload attachment;
                    if($val['size']  != 0){
                        $file = request()->file($key);
                        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                        if($info){
                            $data[$key]=DS.'uploads'.DS.$info->getSaveName();
                        }else{
                            $data[$key]='';
                        }
                    }
                }
            }
            foreach ($tmp as $k => $v){
                if(!in_array($v,array_keys($data))){
                    //echo $v.'is not exits';
                    $data[$v]='';
                }
            }
            foreach ($data as $key => $val){
                if(is_array($val)){
                    $val=implode(',',$val);
                }
                db('system')->where(['ename'=>$key])->update(['value'=>$val]);
            }
            $this->success('更新成功',url('system/index'),['msg'=>'update success'],2000);
        }

    }

    public function lst(){
        $system = db('system')->paginate(10);
        $this->assign('system',$system);
        return $this->fetch();
    }

    public function add(){
        if(Request::instance()->isGet()){
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $data=input("post.",'','htmlspecialchars');
            $validate = Loader::validate('System');
            if(!$validate->scene('add')->check($data)){
                $this->error($validate->geterror());
            }
            $query=db('system')->insert($data);
            if($query){
                $this -> success('配置项添加成功',url('system/lst'));
            }else{
                $this -> error('配置项添加失败');
            }
        }
    }

    public function edit(){
        if(Request::instance()->isGet()){
            if($id= Request::instance()->param('id')){
                $data=db('system')->where(['id'=>$id])->find();
                $this->assign('data',$data);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $data=input("post.",'','htmlspecialchars');
            $validate = Loader::validate('System');
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->geterror());
            }
            $query=db('system')->update($data);
            if($query){
                $this -> success('配置项修改成功',url('system/lst'));
            }else{
                $this -> error('配置项修改失败');
            }
        }
    }

    public function  delete(){
        if(Request::instance()->isGet()){
            $id=input('id',0,'int');
            if(db('system')->where(['id'=>$id])->delete()){
                echo 1;
            }else{
                echo 0;
            }
        }

    }
}