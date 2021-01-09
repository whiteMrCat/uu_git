<?php

namespace api\admin\controller;

use api\admin\model\UserModel;
use cmf\controller\RestBaseController;

class UserController extends RestBaseController
{
    public function login()
    {
        $data = array(
            'username'  => input('post.username'),
            'password'  => input('post.password')
        );
        $user_model = new UserModel();
        $user = $user_model->userInfo($data['username']);
        // 判断用户是否存在
        if ($user) {
            // 判断用户密码是否正确
            if (cmf_compare_password($data['password'], $user['user_pass'])) {
                $this->success('登录成功', $user);
            } else {
                $this->error('密码错误');
            }
        } else {
            $this->error('该用户不存在');
        }
    }
}