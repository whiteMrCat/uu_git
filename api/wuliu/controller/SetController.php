<?php

namespace api\wuliu\controller;

use cmf\controller\RestBaseController;
use think\Db;

class SetController extends RestBaseController
{

    // 配置信息
    public function index()
    {
        $banner = Db::name('z_banner')->select();
        $set = Db::name('z_set')->find();
        $car_type = Db::name('z_type')->where('type', 0)->select();
        $way_type = Db::name('z_type')->where('type', 1)->select();
        $goods_type = Db::name('z_type')->where('type', 2)->select();
        $brand = Db::name('z_type')->where('type', 3)->select();
        $medium = Db::name('z_type')->where('type', 4)->select();
        $data = array(
            'banner' => $banner,
            'set' => $set,
            'car_type' => $car_type, //车辆类型
            'way_type' => $way_type, // 路线类型
            'goods_type' => $goods_type, // 货物类型
            'brand' => $brand, // 品牌
            'medium' => $medium, // 介质
        );
        $this->success('获取成功', $data);
    }

    // 上传
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $files = request()->file();
        $data = [];
        foreach($files as $file){
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $file->move(CMF_ROOT . 'public/upload');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
//                echo $info->getExtension();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getFilename();
                array_push($data, $info->getSaveName());
            }else{
                // 上传失败获取错误信息
//                echo $file->getError();
            }
        }
        $this->success('上传成功', $data);
        // 移动到框架应用根目录/uploads/ 目录下
//        $info = $file->move(CMF_ROOT . 'public/upload');
//        if($info){
//            // 成功上传后 获取上传信息
//            // 输出 jpg
////            echo $info->getExtension();
//            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
////            echo $info->getSaveName();
//            $this->success('上传成功', $info->getSaveName());
//            // 输出 42a79759f284b767dfcb2a0197904287.jpg
////            echo $info->getFilename();
//        }else{
//            // 上传失败获取错误信息
//            echo $file->getError();
//        }
    }
}