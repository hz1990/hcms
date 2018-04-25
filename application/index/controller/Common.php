<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\3\9 0009
 * Time: 9:47
 */

namespace app\index\controller;


use think\Controller;

class Common extends Controller
{
    public $template;
    public $resource;
    public function __construct()
    {
        parent::__construct();
        //获取配置
        $viewPath = config('template.view_path');
        $template = db('system')->where(['ename'=>'template'])->value('value');
        $this ->template = $viewPath.'/'.$template;
        $resource = "__STATIC__/index/".$template;
        //获取栏目数据
        $cate=db('Category')->field('id,cate_name,parent_id,type,link')->where(['parent_id'=>0,'status'=>1])->select();
        foreach ($cate as $key => $val){
            if(db('Category')->where(['parent_id'=>$val['id'],'status'=>1])->find()){
                $cate[$key]['son']=db('Category')->field('id,cate_name,parent_id,type,link')->where(['parent_id'=>$val['id'],'status'=>1])->select();
            }
        }
        //网站配置数据
        $system= $this->getSys();
        //底部数据
        $footerCate = db('Category')->field('id,cate_name,parent_id,type,link')->where(['parent_id'=>0,'status'=>1,'showInBottom'=>1])->select();
        foreach ($footerCate as $key => $val){
            if(db('Category')->where(['parent_id'=>$val['id'],'status'=>1])->find()){
                $footerCate[$key]['son']=db('Category')->field('id,cate_name,parent_id,type,link')->where(['parent_id'=>$val['id'],'status'=>1])->select();
            }
        }
        $this->assign([
            'footerCate'=>$footerCate,
            'system'=>$system,
            'cate'=>$cate,
            'resource'=>$resource,
            'template'=>$template,
        ]);
    }

    public function getSys(){
        $system= [];
        $sys = db('system')->select();
        foreach($sys as $val){
            $system[$val['ename']]=$val['value'];
        }
        return $system;
    }

    public function getPosition($cid){
        static $position = array();
        $cate = db('category')->field('id,cate_name,parent_id')->select();
        $activeCate=db('category')->field('id,cate_name,parent_id')->find($cid);
        $position[]=$activeCate;
        foreach ($cate as $key=>$val){
            if($val['id']==$activeCate['parent_id']){
                $position[]=$val;
                if($val['parent_id']!=0){
                    $this->getPosition($val['parent_id']);
                }
            }
        }
        return array_reverse($position);
    }

    public function getTopCate($id){
        $data = db('category')->field('id,parent_id,cate_name,jump')->select();
        return $this->_getTopCate($id,$data);
    }

    private function _getTopCate($id,$data){
        $activeCate = db('category')->field('id,parent_id,cate_name,jump')->find($id);
        if($activeCate['parent_id']==0){
            return $id;
        }
        static $topCate = 0;
        foreach ($data as $key=>$val){
            if($val['id'] == $activeCate['parent_id']){
                $topCate=$val['id'];
                if($val['parent_id']!=0){
                    $this->getTopCate($val['id'],$data);
                }
            }
        }
        return $topCate;
    }
}