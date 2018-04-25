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

class AuthRule extends Model{
    //栏目树形结构
    public function tree(){
        $ruleRes = $this->order('id desc')->select();
        foreach ($ruleRes as $key => $val){
            if($val['pid'] == 0){
                $ruleRes[$key]['parent']='顶级规则';
            }else{
                $ruleRes[$key]['parent']=$this->where(['id'=>$val['pid']])->value('name');
            }
        }
        return $this->sort($ruleRes);
    }

    //递归调用
    public function sort($ruleRes,$id=0,$level=1){
        static $arr = array();
        foreach ($ruleRes as $key => $val){
            if($val['pid'] == $id){
                $val['level']=$level;
                $arr[]=$val;
                $this->sort($ruleRes, $val['id'], $level+1);
            }
        }
        return $arr;
    }

    public function childrenids($id){
        $data = $this->field('id,pid')->select();
        return $this->_childrenids($data,$id);
    }

    private function _childrenids($data,$id){
        static $arr=array();
        foreach ($data as $key => $val){
            if($val['pid'] == $id){
                $arr[]=$val['id'];
                $this->_childrenids($data,$val['id']);
            }
        }
        return $arr;
    }
}