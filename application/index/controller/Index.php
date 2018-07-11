<?php
namespace app\index\controller;

use app\common\util\JjyApi;
use think\View;

class Index extends Main {
    // 商城首页
    public function index() {
        $param = pd($_GET);

        if (isset($param['barid']) || !empty($param['barid'])) {
            $_SESSION['netbarinfo'] = ['barid' => $param['barid']];
        } else {
            $_SESSION['netbarinfo'] = ['barid' => 0];
        }

        $view = new View();
        return $view->fetch('index/index');
    }
}
