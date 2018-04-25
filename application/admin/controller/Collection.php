<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017\11\27 0027
 * Time: 9:24
 */
namespace  app\admin\controller;

use QL\QueryList;
use think\Request;
use think\Loader;
use think\Db;

class Collection extends CheckLogin{
    public function lst(){
        //模型数据
        $collection = db('collection')
            ->alias('a')
            ->join('model b','a.model_id = b.id')
            ->field('a.*,b.model_name,b.table_name')
            ->paginate(20);
        $this->assign('collection',$collection);
        return $this->fetch();
    }

    public function add(){
        if(Request::instance()->isGet()){
            $model = db('model')->select();
            $this->assign('model',$model);
            return $this->fetch();
        }else{
            $post = input('post.','','htmlspecialchars');
            $data['collection_name'] = $post['collection_name'];
            $data['model_id'] = $post['model_id'];
            $data['input_encoding'] = $post['input_encoding'];
            $data['output_encoding'] = $post['output_encoding'];
            $data['addtime'] = time();
            $data['list_rule'] = [
                'list_url' => $post['list_url'],
                'start_page' => $post['start_page'],
                'end_page' => $post['end_page'],
                'step' => $post['step'],
                'range' => $post['range'],
                'rule' => $post['rule'],
                'litpic_rule' => $post['litpic_rule'],
            ];
            $data['list_rule'] = json_encode($data['list_rule']);
            $query = db('collection')->insertGetId($data);
            if($query){
                $this->redirect('collection/add_content_rule',['model_id'=>$post['model_id'],'id'=>$query]);
            }else{
                $this->error('数据出错');
            }
        }
    }

    public function add_content_rule(){
        if(Request::instance()->isGet()){
            $model_id = Request::instance()->param('model_id');
            $id = Request::instance()->param('id');
            $fields = db('model_field')->field('id,field_cname,field_ename')->where(['model_id'=>$model_id])->select();
            $this->assign('fields',$fields);
            $this->assign('id',$id);
            return $this->fetch();
        }else{
            $post = input('post.','','htmlspecialchars');
            foreach($post as $key => $val){
                if($key!='id'&& $key!='range'){
                    $array[$key]=array_filter($val);
                }
            }
            $array=array_filter($array);
            $data['content_rule'] = json_encode($array);
            $data['range'] = $post['range'];
            $query = db('collection')->where(['id'=>$post['id']])->update($data);
            if($query){
                $this->success('添加成功',url('collection/lst'),'',2000);
            }else{
                $this->error('添加失败');
            }
        }
    }
    //删除节点
    public function  delete(){
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            if(Db::name('collection')->where(['id'=>$id])->delete()){
                //删除临时表内容
                db('collection_temp')->where(['c_id'=>$id])->delete();
                $this->success('删除成功',url('collection/lst'),'',2000);
            }else{
                $this->error('删除失败');
            }
        }
    }
    //采集节点修改
    public function edit(){
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            $model = db('model')->select();
            $this->assign('model',$model);
            $data = db('collection')->where(['id'=>$id])->find();
            $data['list_rule']= json_decode($data['list_rule'],true);
            $data['content_rule']= json_decode($data['content_rule'],true);
            $this->assign('collection',$data);
            return $this->fetch();
        }else{
            $post = input('post.','','htmlspecialchars');
            $data['id'] = $post['id'];
            $data['collection_name'] = $post['collection_name'];
            $data['model_id'] = $post['model_id'];
            $data['input_encoding'] = $post['input_encoding'];
            $data['output_encoding'] = $post['output_encoding'];
            $data['addtime'] = time();
            $data['list_rule'] = [
                'list_url' => $post['list_url'],
                'start_page' => $post['start_page'],
                'end_page' => $post['end_page'],
                'step' => $post['step'],
                'range' => $post['range'],
                'rule' => $post['rule'],
                'litpic_rule' => $post['litpic_rule'],
            ];
            $data['list_rule'] = json_encode($data['list_rule']);
            $query = db('collection')->update($data);
            if($query){
                $this->redirect('collection/edit_content_rule',['model_id'=>$post['model_id'],'id'=>$data['id']]);
            }else{
                $this->error('修改失败');
            }
        }
    }
    //采集节点内容采集规则修改
    public function edit_content_rule(){
        if(Request::instance()->isGet()){
            $model_id = Request::instance()->param('model_id');
            $id = Request::instance()->param('id');
            $fields = db('model_field')->field('id,field_cname,field_ename')->where(['model_id'=>$model_id])->select();
            $this->assign('fields',$fields);
            $this->assign('id',$id);
            $data = db('collection')->where(['id'=>$id])->find();
            $data['list_rule']= json_decode($data['list_rule'],true);
            $data['content_rule']= json_decode($data['content_rule'],true);
            $this->assign('collection',$data);
            return $this->fetch();
        }else{
            $post = input('post.','','htmlspecialchars');
            foreach($post as $key => $val){
                if($key!='id'&& $key!='range'){
                    $array[$key]=array_filter($val);
                }
            }
            $array=array_filter($array);
            $data['content_rule'] = json_encode($array);
            $data['range'] = $post['range'];
            $query = db('collection')->where(['id'=>$post['id']])->update($data);
            if($query){
                $this->success('修改成功',url('collection/lst'),'',2000);
            }else{
                $this->error('修改失败');
            }
        }
    }

    //采集
    public function collection()
    {
        if(Request::instance()->isGet()){
            $id= Request::instance()->param('id');
            //采集数据
            $tmp = db('collection')->where(['id'=>$id])->find();
            //collection begin
            $listRule = json_decode($tmp['list_rule'],true);
            //采集地址
            $list_url = $listRule['list_url'];
            //开始采集
            $start_page = $listRule['start_page'];
            //结束采集
            $end_page = $listRule['end_page'];
            //步长
            $step = $listRule['step'];
            //采集范围
            $range = $listRule['range'];
            //网址采集规则
            $list_rule = json_decode(htmlspecialchars_decode($listRule['rule']),true);
            //缩略图规则
            $litpic_rule = json_decode(htmlspecialchars_decode($listRule['litpic_rule']),true);
            //合成采集规则
            $rule_list = array_merge($list_rule,$litpic_rule);
            //网址处理
            $url = array();
            for($i=$start_page; $i<=$end_page; $i+=$step){
                $url[] = str_replace('(*)',$i,$list_url);
            }
            //采集数据
            foreach ($url as $key => $val){
                $data[] = QueryList::get($val)->rules($rule_list)->range($range)->query()->getData()->all();
            }
            //数组重构
            $array =array();
            foreach($data as $key =>$val){
                foreach($val as $k => $v){
                    $array[]=$v;
                }
            }
            //内容采集配置
            $content_rule=json_decode($tmp['content_rule'],true);
            //内容采集
            $data_content = array();
            foreach ($array as $ke => $value){
                $data_content[] = QueryList::get($value['url'])->rules($content_rule)->range($tmp['range'])->query()->getData()->all();
                $data_content[$ke][0]['url']=$value['url'];
                $data_content[$ke][0]['litpic']=$value['litpic'];
            }
            //内容数组重构
            $array_content =array();
            foreach($data_content as $key =>$val){
                foreach($val as $k => $v){
                    $array_tmp['c_id']=$id;
                    $array_tmp['title']=$v['title'];
                    $array_tmp['url']=$v['url'];
                    $array_tmp['addtime']=time();
                    $array_tmp['result']=json_encode($v);
                    $array_content[]=$array_tmp;
                }
            }
            //采集数据入库(防止重复采集)
            foreach ($array_content as $k1 =>$v1){
                if(!db('collection_temp')->where(['title'=>$v1['title'],'c_id'=>$id])->find()){
                    db('collection_temp')->insert($v1);
                }
            }
            return 1;
        }
    }

    //采集的数据
    function collection_data_list(){
        if(Request::instance()->isGet()){
            $data= db('collection_temp')->alias('a')->join('collection b','a.c_id = b.id')->field('a.c_id,b.collection_name,b.model_id')->group('c_id')->paginate(20);
            $this->assign('collection',$data);
            $cate=db('Category')->select();
            $this->assign('cate',$cate);
            return $this->fetch();
        }
    }

    //数据导出
    function output(){
        if(request()->isAjax()){
            if(request()->isGet()){
                $c_id= Request::instance()->param('c_id');
                $cate_id= Request::instance()->param('cate_id');
                //所属模型
                $model_id = db('category')->where(['id'=>$cate_id])->value('model_id');
                //附加表
                $table_name = db('model')->where(['id'=>$model_id])->value('table_name');
                //数据导出到指定栏目
                $main_fields = array('title','litpic','content','writer','source','attribute','click','keywords','description','url');
                $data = db('collection_temp')->field('export,result')->where(['c_id'=>$c_id,'export'=>0])->select();
                $main = array();//主表数据
                $auxiliary = array();//附表数据
                foreach($data as $key => $val){
                    $result = json_decode($val['result'],true);
                    foreach ($result as $k => $v){
                        if(in_array($k,$main_fields)){
                            if($k == 'url'){
                                $main['source'] =$v;
                            }else{
                                $main[$k]=$v;
                            }
                        }else{
                            $auxiliary[$k]=$v;
                        }
                    }
                    $main['cate_id']=$cate_id;
                    $main['model_id']=$model_id;
                    $main['time']=time();
                    $aid = db('archives')->insertGetId($main);
                    $auxiliary['aid']=$aid;
                    db($table_name)->insert($auxiliary);
                }
                db('collection_temp')->where(['c_id'=>$c_id])->setField('export',1);
                return 1;
            }
        }
    }

    function delete_c_data(){
        if(request()->isGet()){
            $id= Request::instance()->param('id');
            if(Db::name('collection_temp')->where(['c_id'=>$id])->delete()){
                $this->success('删除成功',url('collection/collection_data_list'),'',2000);
            }else{
                $this->error('删除失败');
            }
        }
    }
}