<?php
namespace api\wuliu\controller;
use api\wuliu\controller\PublicController;
use api\wuliu\model\FiltrateModel;
use api\wuliu\model\InfoInviteModel;
use think\App;
class InfoInviteController extends PublicController
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $info = new InfoInviteModel();
        // 获取每天已结束置顶车源
        $endTopInfo = $info->getEndTopInfo();
        // 批量结束置顶
        $info->endInfoAll($endTopInfo);
        // 获取已预约置顶车源
        $makeTopInfo = $info->getMakeTopInfo();
        // 批量置顶信息
        $info->topInfoAll($makeTopInfo);
    }

    // 招聘信息
    public function index()
    {
        $sceen = new FiltrateModel();
        $page = input('post.page');
        $page_size = input('post.page_size');
        $filters = $sceen->getSceen(input('post.filters/a'));
        $info = new InfoInviteModel();
        $this->success('招聘信息', $info->getInfo($filters, $page, $page_size));
    }

    // 添加招聘
    public function addInfo()
    {
        $info = new InfoInviteModel();
        $user = $this->user;
        $data = array(
            'uid' => $user['id'],
            'name' => input('post.name'),
            'start_salary' => input('post.start_salary'),
            'end_salary' => input('post.end_salary'),
            'people_nums' => input('post.people_nums'),
            'number_year' => input('post.number_year'),
            'welfare' => input('post.welfare'),
            'school' => input('post.school'),
            'address' => input('post.address'),
            'info' => input('post.info'),
            'contact' => input('post.contact'),
            'mobile' => input('post.mobile'),
        );
        $res = $info->add($data);
        if ($res) {
            $this->success('保存成功', $res);
        } else {
            $this->error('保存失败');
        }

    }

    // 置顶招聘
    public function topCarInfo()
    {
        $user = $this->user;
        $info = new InfoInviteModel();
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

    // 更新招聘
    public function update_info() {
        $info = new InfoInviteModel();
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
        $model = new InfoInviteModel();
        $res = $model->delInfo($id);
        if ($res) {
            $this->success('成功');
        } else {
            $this->error('失败');
        }
    }
}