<?php

namespace api\wuliu\model;

use think\Db;
use think\Model;

class FiltrateModel extends Model
{
    function getSceen($filters)
    {
        if ($filters) {
            $data = [];
            foreach ($filters as $key => $val) {
                if (is_array($filters[$key]) && !empty($val)) {
                    $data[$key] = [$val[0], $val[1]];
                } elseif (!empty($val)) {
                    $data[$key] = $val;
                }
            }
            return $data;
        } else {
            return [];
        }
    }
}