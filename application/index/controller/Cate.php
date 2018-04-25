<?php
namespace app\index\controller;

class Cate extends Common
{
    public function index()
    {
        $cid = input('id');
        //当前栏目数据
        if($jump =db('category')->where(['id'=>$cid])->value('jump')){
            $cid= $jump;
            $config = db('category')->where(['id'=>$cid])->find();
        }else{
            $config = db('category')->where(['id'=>$cid])->find();
        }
        $table_name= db('model')->where(['id'=>$config['model_id']])->value('table_name');
        switch($config['type']){
            case 1:
                $template = $config['list_template'];
                if($children=db('category')->where(['parent_id'=>$cid])->find()){
                    $ids=db('category')->where(['parent_id'=>$cid])->column('id');
                    $ids[]=$cid;
                    $data=db('archives')->alias('a')->join($table_name.' b','b.aid=a.id')->where(['a.cate_id'=>['in',$ids]])->paginate(10);
                }else{
                    $data=db('archives')->alias('a')->join($table_name.' b','b.aid=a.id')->where(['a.cate_id'=>$cid])->paginate(10);
                }
                break;
            case 2:
                $template = $config['index_template'];
                $data = $config['content'];
                break;
            case 3:
                $url = $config['link'];
                return $this ->redirect($url);
        }
        //侧边栏栏目数据
        $leftCate = $this->getCate($cid);
        //面包屑数据
        $position = $this->getPosition($cid);
        $this ->assign([
            'leftCate'=>$leftCate,
            'data'=>$data,
            'cateRes'=>$config,
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
