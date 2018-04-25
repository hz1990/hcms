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

class Model extends CheckLogin{
    public function lst(){
        $data = db('model')->paginate(10);
        $this->assign('model',$data);
        return $this->fetch();
    }

    public function add(){
        if(Request::instance()->isGet()){
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $data = input('post.','','htmlspecialchars');
            $validate = Loader::validate('model');
            if(!$validate->check($data)){
                $this->error($validate->geterror());
            }
            $table = config('database.prefix').$data['table_name'];
            $sql = "CREATE TABLE {$table} (`aid` INT unsigned NOT NULL) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;";
            $query = db('model')->insert($data);
            if($query){
                //自动创建数据表
                Db::execute($sql);
                $this->success('模型添加成功',url('model/lst'),'',2000);
            }else{
                $this->error('模型添加失败');
            }
        }
    }

    public function edit(){
        if(Request::instance()->isGet()){
            if($id= Request::instance()->param('id')){
                $data=db('model')->where(['id'=>$id])->find();
                $this->assign('data',$data);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $data=input("post.",'','htmlspecialchars');
            $validate = Loader::validate('model');
            if(!$validate->check($data)){
                $this->error($validate->geterror(),'','',2000);
            }
            $temp = db('model')->where(['id'=>$data['id']])->find();
            if($data['table_name'] !=$temp['table_name']){
                //修改表名的操作
                $table_name_old = config('database.prefix').$temp['table_name'];
                $table_name_new = config('database.prefix').$data['table_name'];
                $sql = "ALTER TABLE {$table_name_old} RENAME {$table_name_new};";
                Db::execute($sql);
            }
            $query=db('model')->update($data);
            if($query){
                $this -> success('模型修改成功',url('model/lst'),'',2000);
            }else{
                $this -> error('模型修改失败');
            }
        }
    }

    public function  delete(){
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            $temp = db('model')->where(['id'=>$id])->find();
            $table_name = config('database.prefix').$temp['table_name'];
            $sql_del = "DROP TABLE IF EXISTS {$table_name}";
            Db::execute($sql_del);
            if(db('model')->where(['id'=>$id])->delete()){
                $this->success('删除成功',url('model/lst'),'',2000);
            }else{
                $this->error('删除失败',url('model/lst'));
            }
        }

    }

    //切换状态
    public function turn(){
        if(Request::instance()->isGet()){
            $id=input('id',0,'int');
            $status=db('model')->where(['id'=>$id])->field('status')->find();
            if($status['status'] == 1){
                db('model')->where(['id'=>$id])->setField('status',0);
                return 0;
            }else{
                db('model')->where(['id'=>$id])->setField('status',1);
                return 1;
            }
        }
    }
}