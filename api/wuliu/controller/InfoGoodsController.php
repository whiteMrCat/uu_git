<?php

namespace api\wuliu\controller;

use api\wuliu\controller\PublicController;
use api\wuliu\model\InfoGoodsModel;
use api\wuliu\model\FiltrateModel;
use think\App;

class InfoGoodsController extends PublicController
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $info = new InfoGoodsModel();
        // 获取每天已结束置顶车源
        $endTopInfo = $info->getEndTopInfo();
        // 批量结束置顶
        $info->endInfoAll($endTopInfo);
        // 获取已预约置顶车源
        $makeTopInfo = $info->getMakeTopInfo();
        // 批量置顶信息
        $info->topInfoAll($makeTopInfo);
    }

    // 货源信息
    public function index()
    {
        $sceen = new FiltrateModel();
        $page = input('post.page');
        $page_size = input('post.page_size');
        $filters = $sceen->getSceen(input('post.filters/a'));
        $info = new InfoGoodsModel();
        $this->success('货源信息', $info->getInfo($filters, $page, $page_size));
    }

    // 添加货源
    public function addInfo()
    {
        $info = new InfoGoodsModel();
        $user = $this->user;
        $data = array(
            'uid' => $user['id'],
            'startAddress' => input('post.startAddress'),
            'endAddress' => input('post.endAddress'),
            'contact' => input('post.contact'),
            'mobile' => input('post.mobile'),
            'weight' => input('post.weight'),
            'freight' => input('post.freight'),
            'goods_name' => input('post.goods_name'),
            'goods_type' => input('post.goods_type'),
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

    // 置顶货源
    public function topCarInfo()
    {
        $user = $this->user;
        $info = new InfoGoodsModel();
        $id = (int)input('post.id');
        $startTime = input('post.startTime');
        $day = (int)input('post.day');
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

    // 更新货源
    public function update_info() {
        $info = new InfoGoodsModel();
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
        $model = new InfoGoodsModel();
        $res = $model->delInfo($id);
        if ($res) {
            $this->success('成功');
        } else {
            $this->error('失败');
        }
    }
}