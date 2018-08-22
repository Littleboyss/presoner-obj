<?php
namespace app\index\controller;

use app\api\controller\Index as api;
use app\common\util\JjyApi;
use app\common\util\TnApi;
use think\View;

class Pub extends Main {
	public function test(){
        $redis = \redis();

        $param = pd($_GET);

        if ($param['tongxinkey'] == '339681850') {
            $log_array = $redis->zRange('PageOpenErrorLog:' . date('Y-m-d'), 1, -1);
            var_dump($log_array);
            die;
        }
	}

	public function login() {
        $param           = pd($_POST);
        $data            = [];
        $data['userid']  = $param['userid'];
        $data['ssid']    = $param['ssid'];
        $tnapi           = new TnApi();
        $tn_data_return  = $tnapi->getData ('http://tuc.te6-api.net:9527/api/user/auth', $data);


        if ($tn_data_return['errno'] !== 0 || !$tn_data_return['data']) {
            $this->re(['code' => 0, 'message' => "登录失败3" . json_encode($tn_data_return), 'data' => []]);
        }

        // 登录成功 发送登录信息并获取绑定的第三方账号信息
        $param                      = $tn_data_return['data'];
        $session_member             = $param;
        $session_member['face']     = (isset($param['avatar']) && $param['avatar'] != '') ? $param['avatar'] : '/Public/market/images/pto1.png';
        $path_info                  = urlencode(encrypt(json_encode([['name' => 'userid', 'value' => $param['userid']],['name' => 'username', 'value' => $param['username']],['name' => 'from', 'value' => 'web'], ['name' => 'ssid', 'value' => $param['ssid']]]), "1asd!kfj89wQ#%K@WzKcEdO@"));
        //$session_member['jump_url'] = "http://uc.te6.com/index.php/user/baseinfo.html?passinfo=" . $path_info;
        $session_member['jump_url'] = "http://uc.te6.com";

        $jjyApi = new JjyApi();

        $login_return = $jjyApi->login($param['ssid'], $param['userid']);

        if (intval($login_return['status']) !== 0 && intval($login_return['status']) !== 3) {
            $this->re(['code' => 0, 'message' => "登录失败1", 'data' => []]);
        }

        if (intval($login_return['status']) === 0) {
            $session_member['token'] = $login_return['data'][1];
            $steam_user_return       = $jjyApi->steamInfo($session_member['token']);

            if (intval($steam_user_return['status']) === 0) {
                $session_member['steamuser'] = $steam_user_return['data']['steamuser'];
            } else if (intval($steam_user_return['status']) === 2) {
                $session_member['steamuser'] = '';
            } else {
                $this->re(['code' => 0, 'message' => '登录失败2', 'data' => []]);
            }
        } else {
            $session_member['token']     = '';
            $session_member['steamuser'] = '';
        }

        session("member", $session_member);

        $return = [
            'userid'    => $session_member['userid'],
            'nickname'  => $session_member['nickname'],
            'phone'     => $session_member['phone'],
            'ssid'      => $session_member['ssid'],
            'face'      => $session_member['face'],
            'username'  => $session_member['username'],
            'steamuser' => $session_member['steamuser'],
        ];

        $this->re(['code' => 1, 'message' => '登录成功', 'data' => $return]);
    }

    //注销
	public function logout(){
		session("member",NULL);

		$href = p('href');

		if ($href != "") {
			redirect(base64_decode($href));
		} else {
			return $this->success("退出成功!","/");
		}
	}

	// 获取验证码
    public function verify() {
        $verifykey = p('verifykey');
        $ssid      = session_id();

        $class = new \ReflectionClass('app\\common\\util\\Verify');
        $verify_class = $class->newInstanceArgs();

        $verify_class->fontSize  = 17;
        $verify_class->length    = 4;
        $verify_class->useNoise  = true;
        $verify_class->imageH    = 41;
        $verify_class->fontttf   = '5.ttf';
        $verify_class->codeSet   = '123456789';
        $verify_class->entry($verifykey . '_' . $ssid);
    }

    public function checkorder () {
        $param = pd($_POST);

        if (empty($param['orderid'])) {
            $this->error('订单号错误');
        }

        $redis = \redis();
        $this->success('订单已完成');
        if ($redis->get('orderSuccess:' . $param['orderid'])) {
            $this->success('订单已完成');
        } else {
            $this->error('订单尚未完成');
        }
    }
}
