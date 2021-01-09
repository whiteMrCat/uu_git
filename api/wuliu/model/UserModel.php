<?php
namespace api\wuliu\model;

use think\Db;
use think\Model;

class UserModel extends Model
{
    protected $table = 'uu_z_user';
    function user_info($id) {
        $user = $this->where('id', $id)->find();
        if ($user['personal_url'] != null) $user['personal_url'] = unserialize($user['personal_url']);
        if ($user['company_url'] != null) $user['company_url'] = unserialize($user['company_url']);
        if ($user['road'] != null) $user['road'] = unserialize($user['road']);
        if ($user['driver'] != null) $user['driver'] = unserialize($user['driver']);
        return $user;
    }
}