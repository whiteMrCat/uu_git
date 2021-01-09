<?php

namespace api\wuliu\controller;

use api\wuliu\model\OrderModel;
use cmf\controller\RestBaseController;
use EasyWeChat\Factory;
use think\App;
use think\Db;

class WxCallBackController extends RestBaseController
{
    protected $config = [
        // 必要配置
        'app_id' => 'wx7a8004ea2b312e31',
        'mch_id' => '1605062080',
        'key' => '1a17e5c4180a571993d481fc69319e71',   // API 密钥

        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        'cert_path' => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
        'key_path' => 'path/to/your/key',      // XXX: 绝对路径！！！！

        'notify_url' => '',     // 你也可以在下单时单独设置来想覆盖它
    ];

    protected $pay_app;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $url = cmf_get_domain();
        $this->config['notify_url'] = $url . '/api/wuliu/wx_call_back';
        $this->pay_app = Factory::payment($this->config);
    }

    // 微信支付回调地址
    public function index()
    {
        $response = $this->pay_app->handlePaidNotify(function ($message, $fail) {
            $orderModel = new OrderModel();
            $order = $orderModel->findOrder($message['out_trade_no']);
            // 判断用户是否支付成功，SUCCESS为成功，FAIL为失败
            if ($message['result_code'] == 'SUCCESS') {
                // 写入支付log
                Db::name('z_payLog')->insert([
                    'info'  => serialize($message),
                    'time'  => time()
                ]);
                // 判断是否存在订单，code=1为存在，0为不存在
                if ($order['code'] == 1) {
                    // 支付成功，改变订单状态，status=1
                    $res = $orderModel->changeOrder($order['id'], 1);
                    // 判断订单状态是否改变，改变则告诉微信订单已处理，未改变则告诉微信订单未处理，微信会一直请求该接口
                    if ($res) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    // 订单不存在，告诉微信订单已处理
                    return true;
                }
            } else {
                // 支付失败，改变订单状态，status=3
                $res = $orderModel->changeOrder($order['id'], 3);
                // 判断订单状态是否改变，改变则告诉微信订单已处理，未改变则告诉微信订单未处理，微信会一直请求该接口
                if ($res) {
                    return true;
                } else {
                    return false;
                }
            }
        });

        $response->send(); // return $response;
    }
}