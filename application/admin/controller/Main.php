<?php
namespace app\admin\controller;

use think\Request;
use think\Url;

class Main extends \think\Controller
{
    public $request;
    public function __construct()
    {
        parent::__construct();
        $this->request = Request::instance();
        Url::root('/index.php');
        //判断用户是否登陆
        $this->initSessions();

        //判断用户权限
        $this->checkRgbc();
    }

    public function initSessions()
    {
        $userModel = model('Admin');
        $userModel->setCookies();
        //判断是有是否登陆
        if (!session('?id')) {
            $this->redirect('/');
        }
    }
    public function checkRgbc()
    {
        // if(CONTROLLER_NAME == 'Admin'){
        //     //不验证首页后台
        //     return ;
        // }
        $currentPri = $this->request->controller() . '/' .$this->request->action();
        $allowPri   = session('auth');
        // dump($currentPri);
        // dump($allowPri);
        // dump(in_array($currentPri, $allowPri));
        if (session('id') == 1) {
            return;
        }
        if (!in_array($currentPri, $allowPri)) {
            $this->error('你没有此权限');
            exit;
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
}
