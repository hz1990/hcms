<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\3\14 0014
 * Time: 14:04
 */

namespace app\index\controller;


class Article extends Common
{
    public function index(){
        $id = input('id',0,'int');
        $data = db('archives')->where(['id'=>$id])->find();
        $template = db('category')->where(['id'=>$data['cate_id']])->value('content_template');
        $table_name= db('model')->where(['id'=>$data['model_id']])->value('table_name');
        $data = db('archives')->alias('a')->join($table_name.' b','b.aid=a.id')->where(['a.id'=>$id])->find();
        //侧边栏栏目数据
        $leftCate = $this->getCate($data['cate_id']);
        //面包屑数据
        $position = $this->getPosition($data['cate_id']);
        $this->assign([
            'leftCate'=>$leftCate,
            'data'=>$data,
            'position'=>$position,
        ]);
        return $this->fetch( $this ->template.'/'.$template);
    }

    private function getCate($id){
        $parent_id = db('category')->field('id,parent_id,cate_name,jump')->where(['id'=>$id])->value('parent_id');
        if($parent_id !=0){
            $id = $parent_id;
        }
        $Cate = db('category')->field('id,parent_id,cate_name,jump')->find($id);
        $Cate['son'] = db('category')->field('id,parent_id,cate_name,jump')->where(['parent_id'=>$id])->select();
        return $Cate;
    }
}