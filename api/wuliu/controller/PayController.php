<?php

namespace api\wuliu\controller;

use api\wuliu\controller\PublicController;
use api\wuliu\model\InfoCarModel;
use api\wuliu\model\OrderModel;
use EasyWeChat\Factory;
use think\App;

class PayController extends PublicController
{
    protected $config = [
        // 必要配置
        'app_id' => 'wx7a8004ea2b312e31',
        'mch_id' => '1605062080',
        'key' => '1a17e5c4180a571993d481fc69319e71',   // API 密钥

        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        'cert_path' => CMF_ROOT.'public/cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
        'key_path' => CMF_ROOT.'public/cert/apiclient_key.pem',      // XXX: 绝对路径！！！！

        'notify_url' => '',     // 你也可以在下单时单独设置来想覆盖它
    ];

    protected $order = [
        'body' => '',
        'out_trade_no' => '',
        'total_fee' => 0,
        'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
        'openid' => '',
    ];

    protected $pay_app;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $url = cmf_get_domain();
        $this->config['notify_url'] = $url . '/api/wuliu/wx_call_back';
        $this->pay_app = Factory::payment($this->config);
    }

    /**
     * 小程序支付
     * @return array|int
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pay($order)
    {
        $jssdk = $this->pay_app->jssdk;

        $result = $this->pay_app->order->unify($order);

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            $prepayId = $result['prepay_id'];
            $config = $jssdk->sdkConfig($prepayId);
            return $config;
        }

        if ($result['return_code'] == 'FAIL' && array_key_exists('return_msg', $result)) {
            return -1;
        }

        return -1;

    }

    public function code_pay($order)
    {
        $result = $this->pay_app->order->unify($order);
        return $result['code_url'];
    }


}