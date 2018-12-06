<?php
namespace app\index\controller;

use app\common\util\Verify;
use think\Request;

class Index extends \think\Controller
{
    public $request;
    public function _initialize()
    {
        $this->request = Request::instance();
    }
    public function index()
    {

        return $this->fetch('index/index');
    }
    public function getCode()
    {
        ob_clean();
        $captcha           = new Verify();
        $captcha->codeSet  = '123456789'; //字符集
        $captcha->useCurve = false; //不用干扰线
        $captcha->useNoise = false; //不用干扰点
        $captcha->fontttf  = '5.ttf'; //字体
        $captcha->length   = 4;
        $captcha->bg       = array(93, 202, 27); //背景颜色
        return $captcha->entry();
    }
    public function login()
    {
        $request   = $this->request;
        $post_data = $request->post();
        if (!$post_data['name']) {
            $this->error('账号为空');
        }
        if (!$post_data['password']) {
            $this->error('密码为空');
        }
        $User = model('hospital');
        //登陆验证
        $status = $User->login($post_data['name'], $post_data['password']);
        if ($status == 1) {
            $this->error('用户名不存在', 'index');
        } elseif ($status == 2) {
            $this->error('密码错误');
        } elseif ($status == 3) {
            $this->success('登陆成功');
        }
    }
    public function logout()
    {
        // 清除session数据
        session(null);
        setcookie('id', '', time() - 1, '/'); // cookie 清除
        // cookie(null) TP里面才有
        $this->success('退出成功！', 'index');
        exit();
    }
}
