<?php
namespace app\experts\controller;

use think\Request;

class Main extends \think\Controller
{
    public $request;
    public function __construct()
    {
        parent::__construct();
        $this->request = Request::instance();
        //判断用户是否登陆
        $this->initSessions();
        $this->getNowPwd();
    }

    public function initSessions()
    {
        $userModel = model('experts');
        $userModel->setCookies();
        //判断是有是否登陆
        if (!session('?id')) {
            $this->redirect('/');
        }
    }

    public function returnMsg($status, $info, $data = array(), $extra_data = array())
    {
        $items           = array();
        $items['status'] = $status;
        $items['info']   = $info;
        if ($info == '你没有此权限') {
            $items['status'] = 500;
        }
        if (!empty($data)) {
            $items['data'] = $data;
        }
        if (!empty($extra_data)) {
            $items['extra_data'] = $extra_data;
        }
        echo json_encode($items);
        exit;
    }
    // 展示分页
    public function showPages($count, $list, $Map)
    {
        $page = new \Think\Page($count, $list);
        foreach ($Map as $key => $val) {
            $page->parameter[$key] = urlencode($val);
        }
        //$page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
        $page->setConfig('header', '<span>共%TOTAL_ROW%条记录&nbsp;第%NOW_PAGE%页/共%TOTAL_PAGE%页</span>');
        $page->setConfig('first', '首页');
        $page->setConfig('last', '末页');
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        $page->setConfig('link', 'indexpagenumb'); //pagenumb 会替换成页码
        $page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $page->lastSuffix = false; //最后一页不显示为总页数
        $page->rollPage   = 10;
        $show             = $page->show();
        $this->assign("show", $show);
    }
    public function getNowPwd()
    {
        $currentPri = $this->request->controller() . '/' . $this->request->action();
        $this->assign('currentPri', $currentPri);
    }
    // 上传图像
    public function upload()
    {
        $file = request()->file('file');
        $path = '../user/images/';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(['size' => 2048000, 'ext' => 'jpg,png,gif'])->move($path);
        if ($info) {
            echo json_encode(array('error' => 0, 'url' => config('IMG_DOMAIN') . '/images/' . $info->getSaveName()));
        } else {
            // 上传失败获取错误信息
            json_encode(array('error' => 1, 'message' => $file->getError()));
        }
        exit;

    }
}
