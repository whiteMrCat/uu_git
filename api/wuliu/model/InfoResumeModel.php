<?php
namespace api\wuliu\model;

use think\Db;
use think\Model;

class InfoResumeModel extends Model
{
    protected $table = 'uu_z_info_resume';

    /**
     * 获取求职信息
     * @param null $filters
     * @return array|\PDOStatement|string|\think\Collection|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getInfo($filters = null, $page = 1, $page_size = 10) {
        $data = $this->where($filters)->limit(($page - 1) * $page_size, $page_size)->order('id', 'desc')->select();
        return $data;
    }

    /**
     * 添加求职信息
     * @param $data
     * @return array|false|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function add($data) {
        $info_id = $this->insertGetId($data);
        if ($info_id) {
            $info = $this->where('id', $info_id)->find();
            return $info;
        } else {
            return false;
        }
    }

    /**
     * 更新求职
     * @param $id
     * @param $data
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function editor($id, $data) {
        $res = $this->where('id', $id)->update($data);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除信息
     * @param $id
     * @return bool
     * @throws \Exception
     */
    function delInfo($id) {
        $res = $this->where('id', $id)->delete();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 置顶求职
     * @param $id
     * @param $startTime
     * @param int $day
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function topInfo($id, $uid, $startTime, $day = 7) {
        $res = $this->where([
            'id'    => $id,
            'uid'   => $uid
        ])->update([
            'is_top'        => 1,
            'top_startTime' => $startTime,
            'top_endTime'   => $startTime + ($day*24*60*60) // 默认置顶七天
        ]);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 置顶求职-多条
     * @param $lists
     * @return bool
     * @throws \Exception
     */
    function topInfoAll($lists) {
        $data = array();
        foreach ($lists as $val) {
            array_push($data, [
                'id'        => $val['id'],
                'is_top'    => 1,
            ]);
        }
        $res = $this->saveAll($data);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 预置顶求职
     * @param $id
     * @param $startTime
     * @param int $day
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function makeTopInfo($id, $startTime, $day = 7) {
        $res = $this->where('id', $id)->update([
            'is_top'        => 2,
            'top_startTime' => $startTime,
            'top_endTime'   => $startTime + ($day*24*60*60) // 默认置顶七天
        ]);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 结束置顶
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function endInfo($id) {
        $res = $this->where('id', $id)->update([
            'is_top'        => 0,
            'top_startTime' => '',
            'top_endTime'   => ''
        ]);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 结束置顶-多条
     * @param $lists
     * @return bool
     * @throws \Exception
     */
    function endInfoAll($lists) {
        $data = array();
        foreach ($lists as $val) {
            array_push($data, [
                'id'    => $val['id'],
                'top_startTime' => '',
                'top_endTime'   => ''
            ]);
        }
        $res = $this->saveAll($data);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 查询最晚置顶时间
     * @return \think\db\Query
     */
    function getInfoTime() {
        $data = $this->max('top_endTime');
        return $data;
    }

    /**
     * 查询当前置顶数量
     * @return float|int|string
     */
    function getInfoSize() {
        $data = $this->where([
            'is_top'    => 1,
            'status'    => 1
        ])->count();
        return $data;
    }

    /**
     * 获取当天快结束的置顶信息
     * @return array|\PDOStatement|string|\think\Collection|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function todayTopInfo() {
        $start_time = strtotime(date("Y-m-d",time()));
        $end_time = $start_time+60*60*24;
        $data = $this->where('top_endTime', 'between', [$start_time, $end_time])->where('is_top', 1)->select();
        return $data;
    }

    /**
     * 获取已经结束的置顶信息
     * @return array|\PDOStatement|string|\think\Collection|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getEndTopInfo() {
        $time = strtotime(date("Y-m-d",time()));
        $data = $this->where('top_endTime', '<', $time)->select();
        return $data;
    }

    /**
     * 获取未置顶但已预约置顶信息
     * @return array|\PDOStatement|string|\think\Collection|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getMakeTopInfo() {
        $time = strtotime(date("Y-m-d",time()));
        $data = $this->where('top_startTime', '>=', $time)->select();
        return $data;
    }

}