<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\29 0029
 * Time: 15:19
 */

namespace app\admin\model;
use think\Model;
use think\Session;

class Category extends Model{
    //栏目树形结构
    public function tree(){
        $cateRes = $this->order('id desc')->select();
        return $this->sort($cateRes);
    }

    //递归调用
    public function sort($cateRes,$parent_id=0,$level=1){
        static $arr = array();
        foreach ($cateRes as $key => $val){
            if($val['parent_id'] == $parent_id){
                $val['level']=$level;
                $arr[]=$val;
                $this->sort($cateRes, $val['id'], $level+1);
            }
        }
        return $arr;
    }

    public function childrenids($id){
        $data = $this->field('id,parent_id')->select();
        return $this->_childrenids($data,$id);
    }

    private function _childrenids($data,$id){
        static $arr=array();
        foreach ($data as $key => $val){
            if($val['parent_id'] == $id){
                $arr[]=$val['id'];
                $this->_childrenids($data,$val['id']);
            }
        }
        return $arr;
    }
}