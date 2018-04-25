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

class ModelField extends CheckLogin{
    public function lst(){
        if($id= Request::instance()->param('model_id')){
            $model=db('model')->where(['id'=>$id])->find();
            $this->assign('model',$model);
        }else{
            $this->error('参数错误');
        }
        $model_field = db('model_field')
            ->alias('a')
            ->join('model b','a.model_id = b.id')
            ->field('a.*,b.model_name')
            ->where(['a.model_id'=>$id])
            ->paginate(10);
        $this->assign('model_field',$model_field);
        return $this->fetch();
    }

    public function add(){
        if(Request::instance()->isGet()){
            if($id= Request::instance()->param('model_id')){
                $data=db('model')->where(['id'=>$id])->find();
                $this->assign('data',$data);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $data = input('post.','','htmlspecialchars');
            $validate = Loader::validate('ModelField');
            if(!$validate->check($data)){
                $this->error($validate->geterror());
            }
            $data['field_value']=str_replace('，',',',$data['field_value']);
            $query = db('model_field')->insert($data);
            if($query){
                $table_name = db('model')->where(['id'=>$data['model_id']])->value('table_name');
                $table_name=config('database.prefix').$table_name;
                switch ($data['field_type']){
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 6:
                    $field_type = 'varchar(255) not null default ""';
                        break;
                    case 5:
                        $field_type = 'varchar(255) not null default ""';
                        break;
                    case 7:
                        $field_type = 'float not null default 0.0';
                        break;
                    case 8:
                        $field_type = 'int not null default 0';
                        break;
                    case 9:
                        $field_type = 'text';
                        break;
                    default:
                        $field_type = 'varchar(255) not null default ""';
                        break;
                }
                $sql = "ALTER TABLE {$table_name} ADD {$data['field_ename']} {$field_type}";
                Db::execute($sql);
                $this->success('字段添加成功',url('modelField/lst',['model_id'=>$data['model_id']]),'',2000);
            }else{
                $this->error('字段添加失败');
            }
        }
    }

    public function edit(){
        if(Request::instance()->isGet()){
            if($id= Request::instance()->param('id')){
                $data=db('model_field')->where(['id'=>$id])->find();
                $this->assign('data',$data);
            }else{
                $this->error('参数错误');
            }
            return $this->fetch();
        }elseif(Request::instance()->isPost()){
            $data=input("post.",'','htmlspecialchars');
            $validate = Loader::validate('ModelField');
            if(!$validate->check($data)){
                $this->error($validate->geterror(),'','',2000);
            }
            $data['field_value']=str_replace('，',',',$data['field_value']);
            $temp=db('model_field')->where(['id'=>$data['id']])->find();
            $query=db('model_field')->update($data);
            if($query){
                if($temp['field_ename'] !=$data['field_ename'] || $temp['field_type'] != $data['field_type']){
                    $table_name = db('model')->where(['id'=>$data['model_id']])->value('table_name');
                    $table_name=config('database.prefix').$table_name;
                    switch ($data['field_type']){
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                        case 6:
                            $field_type = 'varchar(255) not null default ""';
                            break;
                        case 5:
                            $field_type = 'varchar(255) not null default ""';
                            break;
                        case 7:
                            $field_type = 'float not null default 0.0';
                            break;
                        case 8:
                            $field_type = 'int not null default 0';
                            break;
                        case 9:
                            $field_type = 'text';
                            break;
                        default:
                            $field_type = 'varchar(255) not null default ""';
                            break;
                    }
                    $sql = "ALTER TABLE {$table_name} CHANGE COLUMN {$temp['field_ename']} {$data['field_ename']} {$field_type}";
                    Db::execute($sql);
                }
                $this -> success('字段修改成功',url('modelField/lst',['model_id'=>$data['model_id']]),'',2000);
            }else{
                $this -> error('字段修改失败');
            }
        }
    }

    public function  delete(){
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            $temp = db('model_field')->where(['id'=>$id])->find();
            if(db('model_field')->where(['id'=>$id])->delete()){
                //删除表字段
                $table_name = db('model')->where(['id'=>$temp['model_id']])->value('table_name');
                $table_name=config('database.prefix').$table_name;
                $sql ="ALTER TABLE {$table_name} DROP COLUMN {$temp['field_ename']}";
                Db::execute($sql);
                return 1;
            }else{
                return 0;
            }
        }

    }
}