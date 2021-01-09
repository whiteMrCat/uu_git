<?php
namespace api\wuliu\model;

use think\Db;
use think\Model;

class PublicModel extends Model
{
    public function correct($data) {
        return [
            'code'  => 1,
            'data'  => $data
        ];
    }

    public function fault($data) {
        return [
            'code'  => 0,
            'data'  => $data
        ];
    }
}