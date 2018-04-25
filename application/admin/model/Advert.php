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

class Advert extends Model{
    protected $field=true;
    protected static function init()
    {
        Advert::event('after_insert', function ($advert) {
            $type =input('post.type',0,'int');
            if ($type==2) {
                $link = $_POST['flash_link'];
                //上传文件
                $files = request()->file('flash_src');
                foreach($files as $key => $file){
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                        $data['img_src']=DS . 'uploads'. DS . $info->getSaveName();
                        $data['link']=$link[$key];
                        $data['ad_id']=$advert->id;
                        db('adflash')->insert($data);
                    }else{
                        // 上传失败获取错误信息
                        echo $file->getError();
                    }
                }
            }
        });
    }
}