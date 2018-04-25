<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function check_env()
{
    $env = [
        'sys' => ['操作系统', '无限制', 'linux', PHP_OS, 'success'],
        'php' => ['PHP版本', 'v5.3', 'v5.3', PHP_VERSION, 'success'],
        'gd2' => ['GD库', '2.0', '2.0', '未知', 'success'],
        'attachment' => ['附件上传', '无限制', '2M', '未知', 'success'],
        'disk' => ['磁盘空间', '100M', '>100M', '未知', 'success'],
    ];
    //检查附件上传配置
    if(ini_get('file_uploads')){
        $env['attachment'][3]=ini_get('upload_max_filesize');
    }
    //获取磁盘空间
    if(function_exists('disk_free_space')){
        $env['disk'][3]=floor(disk_free_space(ROOT_PATH)/(1024*1024)).'M';
    }
    //gd2
    $temp = function_exists('gd_info')?gd_info():array();
    if(empty($temp['GD Version'])){
        $env['gd2'][3]='未安装';
        $env['gd2'][4]='error';
        session('error',true);
    }else{
        $env['gd2'][3]=$temp['GD Version'];
    }
    return $env;
}

function check_function(){
    $fun =[
        [ 'mysql_connect', '支持', 'success'],
        [ 'fsockopen', '支持', 'success'],
        [ 'gethostbyname', '支持', 'success'],
        [ 'file_get_contents', '支持', 'success'],
        [ 'mb_convert_encoding', '支持', 'success'],
        [ 'json_encode', '支持', 'success'],
    ];
    foreach ($fun as $val){
        if(!function_exists($val[0])){
            $val[1]="不支持";
            $val[2]="error";
        }
    }
    return $fun;
}

function check_dir_auth(){
    //检查目录和文件是否可写
    $Arr=array(
        array('dir','可写','可写','runtime','success'),
        array('dir','可写','可写','public/uploads','success'),
        array('file','可写','可写','application/config.php','success'),
        array('file','可写','可写','application/database.php','success'),
    );
    foreach ($Arr as $k => $v) {
        if($v[0]=='dir'){
            if(!is_writable(ROOT_PATH.$v[3])){
                if(is_dir(ROOT_PATH.$v[3])){
                    $Arr[$k][2]='不可写';
                    $Arr[$k][4]='error';
                    session('error',true);
                }else{
                    $Arr[$k][2]='不存在';
                    $Arr[$k][4]='error';
                    session('error',true);
                }
            }
        }else{
            if(file_exists(ROOT_PATH.$v[3])){
                if(!is_writable(ROOT_PATH.$v[3])){
                    $Arr[$k][2]='不可写';
                    $Arr[$k][4]='error';
                    session('error',true);
                }
            }else{
                $Arr[$k][2]='不存在';
                $Arr[$k][4]='error';
                session('error',true);
            }
        }
    }
    return $Arr;
}