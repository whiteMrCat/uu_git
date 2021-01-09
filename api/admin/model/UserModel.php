<?php

namespace api\admin\model;

use think\Model;
use think\Db;

class UserModel extends Model
{
    public function userInfo($username) {
        $user = $this->where('user_login', $username)->find();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }
}