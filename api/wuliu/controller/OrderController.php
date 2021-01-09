<?php

namespace api\wuliu\controller;

use api\wuliu\controller\PublicController;
use api\wuliu\controller\PayController;
use api\wuliu\model\OrderModel;

class OrderController extends PublicController
{
    /**
     * 预创建订单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function makeOrder()
    {
        $user = $this->user;
        $price = $this->info_price;
        $pay_way = input('post.pay_way');
        $body = input('post.body');
        $total_fee = input('post.day_size') * $price;
        $openid = $user['open_id'];
        $info_id = input('post.info_id');
        $info_type = input('post.info_type');

        $order = array(
            'pay_way' => $pay_way, // 支付方式  0小程序支付，1二维码支付
            'body' => $body, // 订单说明
            'out_trade_no' => date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8), // 订单号
            'total_fee' => $total_fee, // 订单金额
            'openid' => $openid, // 用户openid
            'status' => 0, // 支付状态：0未支付，1已支付，2取消
            'order_time' => time(), // 订单创建时间
            'pay_time' => '', // 支付时间
            'info_id' => $info_id, // 信息id
            'info_type' => $info_type, // 信息类型
            'uid' => $user['id']
        );

        $orderModel = new OrderModel();
        $res = $orderModel->makeOrder($order);
        if ($res['code'] == 0) {
            $this->error($res['msg']);
        } else {
            $this->success($res['msg'], $order);
        }
    }

    // 支付订单
    public function pay_order()
    {
        $user = $this->user;
        $order = array(
            'body' => input('post.body'),
            'out_trade_no' => input('post.out_trade_no'),
            'total_fee' => input('post.total_fee'),
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $user['open_id']
        );
        $wx_pay = new PayController();
        $res = $wx_pay->pay($order);
        if ($res == -1) {
            $this->error('支付信息创建失败');
        } else {
            $this->success('支付信息创建成功', $res);
        }
    }

    // 订单列表
    public function getOrderList() {
        $page = input('post.page');
        $page_size = input('post.page_size');
        $user = $this->user;
        $orderModel = new OrderModel();
        $data = $orderModel->orderList('', $user['id'], $page, $page_size);
        $this->success('获取成功', $data);
    }

    public function pay_order_code() {

    }
}