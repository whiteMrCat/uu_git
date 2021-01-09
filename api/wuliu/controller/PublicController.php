<?php
namespace api\wuliu\controller;

use cmf\controller\RestBaseController;
use think\App;
use think\Db;

class PublicController extends RestBaseController
{
    protected $token = '';
    protected $user;
    protected $info_size = 10;
    protected $info_price;
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->token = $this->request->header('XX-Token');
        $set = Db::name('z_set')->where('id', 1)->find();
        if ($set) {
            $this->info_price = $set['top_price'];
            $this->info_size = $set['info_size'];
        }
        if (!$this->token) {
            $this->error('暂无权限访问API');
        } else {
            $user = Db::name('z_user')->where('token', $this->token)->find();
            if ($user) {
                $this->user = $user;
            } else {
                $this->error('token错误');
            }
        }
    }
}