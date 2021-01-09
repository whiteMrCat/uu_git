<?php

namespace api\wuliu\controller;

use api\wuliu\model\UserModel;
use cmf\controller\RestBaseController;
use think\Db;

class UserController extends RestBaseController
{
    // 用户登录，未登录直接注册，已注册PC端，再登录小程序端将直接合并账号
    public function login()
    {
        $mobile = input('post.mobile');
        $password = input('post.password');
        $open_id = input('post.open_id');
        $user_table = Db::name('z_user');
        $userModel = new UserModel();
        if (!$open_id) {
            // 手机号登录
            $user = $user_table->where('mobile', $mobile)->find();
            if ($user) {
                if (cmf_compare_password($password, $user['user_pass'])) {
                    $userInfo = $userModel->user_info($user['id']);
                    $this->success('登录成功', $userInfo);
                } else {
                    $this->error('密码错误');
                }
            } else {
                // 手机号注册
                $data = array(
                    'user_name' => input('post.username'),
                    'user_pass' => cmf_password($password),
                    'mobile' => $mobile,
                );
                $user_id = $user_table->insertGetId($data);
                if ($user_id) {
                    $token = cmf_generate_user_token($user_id, 'wxapp');
                    $res = $user_table->where('id', $user_id)->update(['token' => $token]);
                    if ($res) {
                        $user = $user_table->where('id', $user_id)->find();
                        $userInfo = $userModel->user_info($user['id']);
                        $this->success('登录成功', $userInfo);
                    } else {
                        $this->error('用户token更新失败');
                    }
                } else {
                    $this->error('用户创建失败');
                }
            }
        } else {
            // 小程序登录
            $user = $user_table->where('mobile', $mobile)->find();
            if ($user) {
                $isUser = Db::name('z_user')->where([
                    'mobile' => $mobile,
                    'open_id' => $open_id
                ])->find();
                if ($isUser) {
                    $userInfo = $userModel->user_info($user['id']);
                    $this->success('登录成功', $userInfo);
                } else {
                    $res = Db::name('z_user')->where('id', $user['id'])->update([
                        'open_id' => $open_id,
                        'user_name' => input('post.username'),
                        'avatar' => input('post.avatar')
                    ]);
                    if ($res) {
                        $userInfo = Db::name('z_user')->where('id', $user['id'])->find();
                        $userInfo2 = $userModel->user_info($userInfo['id']);
                        $this->success('合并成功', $userInfo2);
                    } else {
                        $this->error('用户openid更新失败');
                    }
                }

            } else {
                $data = array(
                    'user_name' => input('post.username'),
                    'user_pass' => cmf_password('123456'),
                    'mobile' => input('post.mobile'),
                    'open_id' => input('post.open_id'),
                    'avatar' => input('post.avatar'),
                );
                $user_id = $user_table->insertGetId($data);
                if ($user_id) {
                    $token = cmf_generate_user_token($user_id, 'wxapp');
                    $res = $user_table->where('id', $user_id)->update(['token' => $token]);
                    if ($res) {
//                        $userInfo = $user_table->where('id', $user_id)->find();
                        $userInfo = $userModel->user_info($user['id']);
                        $this->success('新建成功', $userInfo);
                    } else {
                        $this->error('用户token更新失败');
                    }
                } else {
                    $this->error('用户创建失败');
                }
            }
        }
    }

    public function wxInfo()
    {
        $code = input('get.code');
        $appid = 'wx7a8004ea2b312e31';
        $appsecret = '99fd38d34161baf2f48a39025283a6fe';
        $res = $this->getOpenId($code, $appid, $appsecret);
        $this->success('状态', json_decode($res));
    }

    public function getOpenId($code, $appid, $appsecret)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $appsecret . '&js_code=' . $code . '&grant_type=authorization_code';
        $jsonRes = file_get_contents($url);
        return $jsonRes;
    }

    // 开通会员
    public function open_vip()
    {
        $id = input('post.id');
        $res = Db::name('z_user')->where('id', $id)->update([
            'type' => 1
        ]);
        if ($res) {
            $this->success('开通成功');
        } else {
            $this->error('开通失败');
        }
    }

    // 更新信息
    public function update_user()
    {
        $id = input('post.id');
        $data = array(
            'user_name' => input('post.user_name'),
            'mobile' => input('post.mobile'),
            'avatar' => input('post.avatar'),
        );
        $res = Db::name('z_user')->where('id', $id)->update($data);
        $user = Db::name('z_user')->where('id', $id)->find();
        if ($res) {
            $this->success('成功', $user);
        } else {
            $this->error('失败');
        }
    }

    // 个人实名认证
    public function update_personal()
    {
        $id = input('post.id');
        $data = array(
            'personal_status' => 1,
            'personal_name' => input('post.personal_name'),
            'personal_url' => serialize(input('post.personal_url/a'))
        );
        $res = Db::name('z_user')->where('id', $id)->update($data);
        $userModel = new UserModel();
        $user = $userModel->user_info($id);
        if ($res) {
            $this->success('成功', $user);
        } else {
            $this->error('失败');
        }
    }

    // 企业实名认证
    public function update_company()
    {
        $id = input('post.id');
        $data = array(
            'company_status' => 1,
            'company_name' => input('post.company_name'),
            'company_url' => serialize(input('post.company_url/a'))
        );
        $res = Db::name('z_user')->where('id', $id)->update($data);
        $userModel = new UserModel();
        $user = $userModel->user_info($id);
        if ($res) {
            $this->success('成功', $user);
        } else {
            $this->error('失败');
        }
    }

    // 道路运输资格认证
    public function update_road()
    {
        $id = input('post.id');
        $data = array(
            'road_status' => 1,
            'road' => serialize(input('post.road/a'))
        );
        $res = Db::name('z_user')->where('id', $id)->update($data);
        $userModel = new UserModel();
        $user = $userModel->user_info($id);
        if ($res) {
            $this->success('成功', $user);
        } else {
            $this->error('失败');
        }
    }

    // 驾照认证
    public function update_driver()
    {
        $id = input('post.id');
        $data = array(
            'driver_status' => 1,
            'driver' => serialize(input('post.driver/a'))
        );
        $res = Db::name('z_user')->where('id', $id)->update($data);
        $userModel = new UserModel();
        $user = $userModel->user_info($id);
        if ($res) {
            $this->success('成功', $user);
        } else {
            $this->error('失败');
        }
    }

    public function getUserInfo() {
        $id = input('post.id');
        $userModel = new UserModel();
        $user = $userModel->user_info($id);
        if ($user) {
            $this->success('成功', $user);
        } else {
            $this->error('未登录');
        }
    }
}