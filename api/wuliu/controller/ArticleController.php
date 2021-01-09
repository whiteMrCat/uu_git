<?php
namespace api\wuliu\controller;

use cmf\controller\RestBaseController;
use api\wuliu\model\ArticleModel;
use think\App;
use think\Db;

class ArticleController extends RestBaseController
{
    // 文章列表
    public function getArticle() {
        $page = input('post.page');
        $page_size = input('post.page_size');
        $articleModel = new ArticleModel();
        $data = $articleModel->lists($page, $page_size);
        $this->success('获取成功', $data);
    }

    // 文章分类
    public function getArticleType() {
        $articleModel = new ArticleModel();
        $data = $articleModel->getType();
        $this->success('获取成功', $data);
    }

    // 文章详情
    public function getArticleInfo() {
        $id = input('post.id');
        $articleModel = new ArticleModel();
        $data = $articleModel->articleInfo($id);
        $this->success('获取成功', $data);
    }
}