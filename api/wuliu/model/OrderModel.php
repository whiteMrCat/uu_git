<?php

namespace api\wuliu\model;

use think\Db;
use think\Model;

class OrderModel extends Model
{
    protected $table = 'uu_z_order';

    /**
     * 创建预订单
     * @param $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function makeOrder($order)
    {
        $info_id = $order['info_id'];
        $res = $this->where('info_id', $info_id)->find();
        if ($res) {
            return [
                'code' => 0,
                'msg' => '该信息已存在'
            ];
        } else {
            $order_id = $this->insertGetId($order);
            if ($order_id) {
                return [
                    'code' => 1,
                    'msg' => $order_id
                ];
            } else {
                return [
                    'code' => 0,
                    'msg' => '预创建订单失败'
                ];
            }
        }

    }

    /**
     * 改变订单状态及支付时间
     * @param $order_id 订单id
     * @param $status 0未支付，1已支付，2取消
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function changeOrder($order_id, $status)
    {
        $res = $this->where('id', $order_id)->update([
            'status' => $status,
            'pay_time' => time() // 更新订单支付时间
        ]);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 查找订单
     * @param $out_trade_no
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function findOrder($out_trade_no)
    {
        $res = $this->where('out_trade_no', $out_trade_no)->find();
        if ($res) {
            return [
                'code' => 1,
                'msg' => $res
            ];
        } else {
            return [
                'code' => 0,
                'msg' => '暂无订单'
            ];
        }
    }

    public function orderList($filters = null, $uid, $page = 1, $page_size = 10) {
        $res = $this->where($filters)->where('uid', $uid)->limit(($page - 1) * $page_size, $page_size)->order('id', 'desc')->select();
        return $res;
    }
}