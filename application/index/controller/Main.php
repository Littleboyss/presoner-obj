<?php
namespace app\index\controller;

use app\common\util\JjyApi;
use think\Url;

class Main extends \think\Controller
{
    protected $user      = [];
    protected $denglu    = -1;
    protected $password  = "";
    protected $pwdpoint  = 0;
    protected $dengluKey = "&^*%##%Gasd";
    protected $tokenKey  = "@i3(3%1lxi^z&Z*8q#eHdi`^";

    function _initialize(){
   		if (empty($_SESSION)) {
   		    session('init_flag', time());
        }
   		\think\Lang::load(APP_PATH . 'common/lang/zh-cn.php');
   		Url::root("/index.php");

        $request = \think\Request::instance();

        define("ACTION_NAME",     $request->action());
        define("CONTROLLER_NAME", $request->controller());

        if (empty(session('member.token'))) {
            session('member', NULL);
        } else {
            $redis = \redis();
            if (empty(session('member.steamuser')) && !$redis->exists('GetSteamInfo:Token:' . session('member.token'))) {
                $jjyApi            = new JjyApi();
                $steam_user_return = $jjyApi->steamInfo(session('member.token'));

                if (intval($steam_user_return['status']) === 0) {
                    session('member.steamuser', $steam_user_return['data']['steamuser']);
                } else {
                    session('member.steamuser', '');
                }

                $redis->set('GetSteamInfo:Token:' . session('member.token'), session('member.token'), 180);
            }
        }
    }

    // 校验登录状态
    protected function checkLogin($flag = true){
        $username = p('username');
        $info     = session("member");

        if (!$info['userid']) {
            return false;
        } else {
            if ($flag && (!$username || $info['username'] != $username)) {
                return false;
            }
        }

        return $info;
    }

    // protected function redirect($url, $params = [], $code = 302){
    //     parent::redirect($url, $params, $code);
    // }

    protected function re($data, $url = null){
   		if($data['code'] === 1){
   			return $this->success($data['message'],$url,$data['data']);
   		}else{
   			return $this->error($data['message'],$url,$data['data']);
   		}
    }
}
