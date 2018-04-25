<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User;
use think\Db;
use think\Session;

class Index extends Common
{
    public function index()
    {
        //banner
        //$banner=db('archives')->where('attribute','like','%f%')->order('id desc')->limit(3)->select();
        $type=db('advert')->where(['status'=>1,'position_id'=>2])->value('type');
        if($type ==2){
            $banner = db('advert')->alias('a')->join('adflash b',"a.id=b.ad_id")->where(['a.status'=>1,'a.position_id'=>2])->select();
        }else{
            $banner=db('advert')->where(['status'=>1,'position_id'=>2])->find();
        }
        //关于我们
        $about = db('category')->where(['id'=>8])->value('content');
        $about = strip_tags(htmlspecialchars_decode($about));
        //技术服务
        $service = db('category')->where(['id'=>11])->value('content');
        $service = strip_tags(htmlspecialchars_decode($service));
        //最新新闻
        if($children=db('category')->where(['parent_id'=>2])->find()){
            $ids=db('category')->where(['parent_id'=>2])->column('id');
            $ids[]=2;
            $option['id']=['in',$ids];
        }else{
            $option['id']=['eq',2];
        }
        $news = db('archives')->alias('a')->join('article b','a.id=b.aid')->where($option)->order('a.id desc')->find();
        $news['body'] = strip_tags(htmlspecialchars_decode($news['body']));
        //产品数据
        $option2['a.cate_id']=['eq',7];
        $option2['a.attribute']=['like','%p%'];
        $product = db('archives')->alias('a')->join('image b','a.id=b.aid')->where($option2)->order('a.id desc')->select();
        $template = $this ->template.'/index.html'; //动态模版加载
        $this->assign([
            'about'=>$about,
            'service'=>$service,
            'news'=>$news,
            'product'=>$product,
            'banner'=>$banner,
        ]);
        return $this->fetch( $template);
    }
}
