<?php

namespace api\wuliu\controller;

use api\wuliu\controller\PublicController;
use api\wuliu\model\FiltrateModel;
use api\wuliu\model\InfoCarModel;
use think\App;

class InfoCarController extends PublicController
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $info = new InfoCarModel();
        // 获取每天已结束置顶车源
        $endTopInfo = $info->getEndTopInfo();
        // 批量结束置顶
        $info->endInfoAll($endTopInfo);
        // 获取已预约置顶车源
        $makeTopInfo = $info->getMakeTopInfo();
        // 批量置顶信息
        $info->topInfoAll($makeTopInfo);
    }

    // 车源信息
    public function index()
    {
        $sceen = new FiltrateModel();
        $page = input('post.page');
        $page_size = input('post.page_size');
        $filters = $sceen->getSceen(input('post.filters/a'));
//        var_dump($filters);
//        exit();
        $info = new InfoCarModel();
        $this->success('车源信息', $info->getInfo($filters, $page, $page_size));
    }

    // 添加车源
    public function addInfo()
    {
        $info = new InfoCarModel();
        $user = $this->user;
        $is_top = (int)input('post.is_top');
        $data = array(
            'uid' => $user['id'],
            'start_address' => input('post.start_address'),
            'end_address' => input('post.end_address'),
            'contact' => input('post.contact'),
            'mobile' => input('post.mobile'),
            'load' => input('post.load'),
            'freight' => input('post.freight'),
            'car_type' => input('post.car_type'),
            'route_type' => input('post.route_type'),
            'start_time' => input('post.start_time'),
            'info' => input('post.info')
        );
        $res = $info->add($data);
        if ($res) {
            $this->success('保存成功', $res);
        } else {
            $this->error('保存失败');
        }

    }

    // 置顶车源
    public function topCarInfo()
    {
        $user = $this->user;
        $info = new InfoCarModel();
        $id = (int)input('post.id');
//        $startTime = input('post.startTime');
        $startTime = time();
//        $day = (int)input('post.day');
        $day = 7;
        // 查询现有置顶信息数量
        $list = $info->getInfoSize();
        if ($list < $this->info_size) {
            // 置顶操作
            $res = $info->topInfo($id, $user['id'], $startTime, $day);
            if ($res) {
                $this->success('置顶成功');
            } else {
                $this->error('置顶失败');
            }
        } else {
            // 置顶数量超过限制，进行预置顶操作
            // 查询数据表中最晚的置顶时间，并自动加一天
            $endMaxTime = $info->getInfoTime() + (1 * 24 * 60 * 60);
            $res = $info->makeTopInfo($id, $endMaxTime, $day);
            if ($res) {
                $this->success('预置顶成功');
            } else {
                $this->error('预置顶失败');
            }
        }
    }

    // 更新车源
    public function update_info() {
        $info = new InfoCarModel();
        $data = array(
            'status' => input('post.status')
        );
        $id = input('post.id');
        $res = $info->editor($id, $data);
        if ($res) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }

    public function del_info() {
        $id = input('post.id');
        $model = new InfoCarModel();
        $res = $model->delInfo($id);
        if ($res) {
            $this->success('成功');
        } else {
            $this->error('失败');
        }
    }

}