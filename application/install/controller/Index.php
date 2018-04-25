<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\3\16 0016
 * Time: 11:04
 */
namespace app\install\controller;

use think\Controller;
class Index extends Controller{
    public function index(){
        //协议
        session('step',0);
        session('error',0);
        return $this->fetch();
    }
    public function step1(){
        if(session('step')!=0 && session('step')!=1 && session('step')!=2 ){
            session('step',0);
            $this->redirect('Index/index');
        }
        $env=check_env();
        $fun=check_function();
        $auth=check_dir_auth();
        session('step',1);
        $this->assign([
            'env'=>$env,
            'fun'=>$fun,
            'auth'=>$auth,
        ]);
        return $this->fetch();
    }
    public function step2(){
        if(request()->isGet()){
            if(session('step')!=1 && session('step')!=2 && session('step')!=3 ){
                session('step',0);
                $this->redirect('Index/index');
            }
            if(session('error')!=0){
                $this->error('配置出错,请更改后重试。');
            }
            session('step',2);
            return $this->fetch();
        }elseif (request()->isPost()){

        }
    }
    public function step3(){
        session('step',3);
        return $this->fetch();
    }
}