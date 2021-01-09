<?php
namespace api\wuliu\model;

use think\Db;
use think\Model;

class ArticleModel extends Model
{
    protected $table = 'uu_z_article';

    /**
     * 文章分类
     * @return array|\PDOStatement|string|\think\Collection|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getType()
    {
        $data = Db::name('z_article_type')->order('id desc')->select();
        return $data;
    }

    /**
     * 文章列表
     * @param null $filters
     * @return array|\PDOStatement|string|\think\Collection|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function lists($filters = null, $page = 1, $page_size = 10)
    {
        $data = $this->where($filters)->limit(($page - 1) * $page_size, $page_size)->order('id', 'desc')->select();
        return $data;
    }

    /**
     * 获取文章详情
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function articleInfo($id)
    {
        $data = $this->where('id', $id)->find();
        return $data;
    }
}