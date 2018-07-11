<?php
namespace app\index\controller;

use app\common\util\JjyApi;
use app\common\util\SteamauthOOP;
use app\common\util\TnApi;
use think\View;

class User extends Main {
    // 个人中心
    public function index() {
        $steam = new SteamauthOOP();

        $user_token = session('member.token');

        $steam->setReturnUrl ($user_token);

        $apiJjy           = new JjyApi();
        $user_info_return = $apiJjy->userInfo($user_token);

        $view  = new View();
        $view->assign('steam',      $steam);
        $view->assign('userInfo',   $user_info_return);
        return $view->fetch('user/index');
    }

    public function checkloginstatus () {
        $member = $this->checklogin();

        if (empty($member)) {
            $this->re(['code' => 0, 'message' => '您未登录，请先登录再报名哦', 'data' => ['nologin' => 1]]);
        } else {
            if (!isset($member['steamuser']) || empty($member['steamuser'])) {
                $this->re(['code' => 1, 'message' => '您尚未绑定steam账号，请绑定steam账号后再进行报名', 'data' => []]);
            } else {
                $this->re(['code' => 1, 'message' => '', 'data' => $member['steamuser']]);
            }
        }
    }

    public function getmatchhistory () {
        $param = pd($_POST);

        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $user_token = '';
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy = new JjyApi();

        if (!isset($param['page']) || empty($param['page'])) {
            $param['page'] = 1;
        } else {
            $param['page'] = intval($param['page']);
        }

        $data   = $apiJjy->getMatchHistory ($user_token, $param['page']);

        if (intval($data['status']) === 0) {
            $list      = $data['data'];
            $pagecount = $data['extra_data'];

            foreach ($list as $key => $val) {
                $val['add_time'] = date('Y-m-d H:i', $val['add_time']);
                $list[$key]      = $val;
            }
        } else {
            $list      = [];
            $pagecount = 0;
        }

        $this->re(['code' => 1, 'message' => '', 'data' => ['list' => $list, 'pagecount' => $pagecount, 'page' => $param['page']]]);
    }

    public function getrewards () {
        $guess_id = p('guessid');

        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
            $user_token = $user_info['token'];
        }

        $apiJjy = new JjyApi();

        $data   = $apiJjy->getPrice ($user_token, $guess_id);

        if (intval($data['status']) !== 0) {
            $this->re(['code' => 0, 'message' => $data['info'], 'data' => []]);
        } else {
            $this->re(['code' => 1, 'message' => $data['info'], 'data' => []]);
        }
    }

    // 绑定steam账号
    public function bindsteamuser () {
        $param = pd($_POST);

        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $user_token = '';
        } else {
            $user_token = $user_info['token'];
        }

        $steam_id = $param['steamid'];

        $apiJjy   = new JjyApi();

        $data     = $apiJjy->userAdd ($user_token, $steam_id);

        if (intval($data['status']) === 0) {
            session('member.steamuser', $param['steamname']);
            $this->re(['code' => 1, 'message' => '绑定成功', 'data' => []]);
        } else {
            session('member.steamuser', '');
            $this->re(['code' => 0, 'message' => $data['info'], 'data' => []]);
        }
    }

    // steam账号解绑
    public function unbindsteamuser () {
        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy = new JjyApi();

        $data   = $apiJjy->userUnbind ($user_token);

        if (intval($data['status']) !== 0) {
            $this->re(['code' => 0, 'message' => $data['info'], 'data' => []]);
        } else {
            $session_info              = session('member');

            $session_info['steamuser'] = '';

            session('member', $session_info);
            $this->re(['code' => 1, 'message' => $data['info'], 'data' => []]);
        }
    }

    // 获取消息列表
    public function getusermessage () {
        $param = pd($_POST);

        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $user_token = '';
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy = new JjyApi();

        if (!isset($param['page']) || empty($param['page'])) {
            $param['page'] = 1;
        } else {
            $param['page'] = intval($param['page']);
        }

        $data   = $apiJjy->getUserMessage ($user_token, $param['page']);

        if (intval($data['status']) === 0) {
            $list      = $data['data'];
            $pagecount = $data['extra_data'];

            foreach ($list as $key => $val) {
                $val['add_time'] = date('Y-m-d H:i:s', $val['add_time']);
                $list[$key]      = $val;
            }
        } else {
            $list      = [];
            $pagecount = 0;
        }

        $this->re(['code' => 1, 'message' => '', 'data' => ['list' => $list, 'pagecount' => $pagecount, 'page' => $param['page']]]);
    }

    // 判定绑定是否成功
    public function checkbindsuccess () {
        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }

        if (\redis()->exists('BindSteamUserSuccess:UserToken:' . $user_token)) {
            $jjyApi            = new JjyApi();
            $steam_user_return = $jjyApi->steamInfo($user_token);

            if (intval($steam_user_return['status']) === 0) {
                session('member.steamuser', $steam_user_return['data']['steamuser']);

                $this->re(['code' => 1, 'message' => '绑定成功', 'data' => session('member.steamuser')]);
            } else {
                session('member.steamuser', '');

                $this->re(['code' => 0, 'message' => '绑定失败', 'data' => []]);
            }
        } else {
            $this->re(['code' => 0, 'message' => '绑定失败', 'data' => []]);
        }
    }

    public function checkhasnewmessage () {
        $user_info = $this->checkLogin();
        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy = new JjyApi();

        $data   = $apiJjy->userHasMessage ($user_token);
       
        if ($data['status'] == 3) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        }
        if (intval($data['status']) !== 0) {
            $this->re(['code' => 0, 'message' => $data['info'], 'data' => []]);
        } else {
            $this->re(['code' => 1, 'message' => $data['info'], 'data' => []]);
        }
    }

    public function checkphone () {
        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_phone = $user_info['phone'];

            if (empty($user_phone)) {
                $this->re(['code' => 0, 'message' => '需要绑定手机', 'data' => []]);
            } else {
                $this->re(['code' => 1, 'message' => '不需要绑定手机', 'data' => []]);
            }
        }
    }

    // 绑定手机
    public function bindphone () {
        $param     = pd($_POST);

        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        }

        $data              = [];
        $data['userid']    = $param['userid'];
        $data['ssid']      = $param['ssid'];
        $data['phone']     = $param['phone'];
        $data['phonecode'] = $param['phonecode'];

        $tnapi             = new TnApi();
        $return            = $tnapi->getData ('http://tuc.te6-api.net:9527/api/user/bindPhone', $data);

        if ($return['errno'] === 0 && $return['data']) {
            session('member.phone', $param['phone']);
            $this->re(['code' => 1, 'message' => '绑定成功', 'data' => []]);
        } else {
            $this->re(['code' => 0, 'message' => '绑定失败', 'data' => []]);
        }
    }

    public function feedback () {
        $param     = pd($_POST);

        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy = new JjyApi();

        $data   = $apiJjy->feedback ($user_token, $param['feedbacknumber'], $param['feedbackcontent']);

        if (intval($data['status']) !== 0) {
            $this->re(['code' => 0, 'message' => $data['info'], 'data' => []]);
        } else {
            $this->re(['code' => 1, 'message' => $data['info'], 'data' => []]);
        }
    }

    // 余额记录
    public function moneyrecord () {
        $param     = pd($_POST);

        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $user_token = '';
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy = new JjyApi();

        if (!isset($param['page']) || empty($param['page'])) {
            $param['page'] = 1;
        } else {
            $param['page'] = intval($param['page']);
        }

        $data = $apiJjy->msgList ($user_token, $param['page']);

        if (intval($data['status']) === 0) {
            $list      = $data['data'];
            $pagecount = $data['extra_data'];

            foreach ($list as $key => $val) {
                $val['class']   = floatval($val['balance']) > 0 ? 'te6_addM' : 'te6_minusM';
                $val['balance'] = sprintf('%.2f', $val['balance'] / 100.0);
                $list[$key]     = $val;
            }
        } else {
            $list      = [];
            $pagecount = 0;
        }

        $this->re(['code' => 1, 'message' => '', 'data' => ['list' => $list, 'pagecount' => $pagecount, 'page' => $param['page']]]);
    }

    // 提现
    public function getmoney () {
        $param = pd($_POST);

        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            if (empty($user_info['phone'])) {
                $this->re(['code' => 2, 'message' => '您尚未绑定手机号，请先绑定手机号再进行提现操作', 'data' => []]);
            }

            $user_phone = $user_info['phone'];
            $user_token = $user_info['token'];
        }

        if (!is_numeric($param['money'])) {
            $this->re(['code' => 0, 'message' => '请输入正确的提现金额', 'data' => []]);
        }

        $apiJjy = new JjyApi();

        $data   = $apiJjy->getMoney ($user_token, $user_phone, $param['money']);

        if (intval($data['status']) !== 0) {
            $this->re(['code' => 0, 'message' => $data['info'], 'data' => []]);
        } else {
            $user_info_return = $apiJjy->userInfo ($user_token);

            $this->re(['code' => 1, 'message' => $data['info'], 'data' => $user_info_return['data']]);
        }
    }
    // 确定token是否过期
    public function check_token () {
        $user_info = $this->checkLogin();
        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }
        $apiJjy = new JjyApi();
        $data   = $apiJjy-> userInfo($user_token);
        if ($data['status'] == 1) {
            $this->re(['code' => 0, 'message' => '请重新登录再操作', 'data' => ['nologin' => 1]]);
        }else {
            $this->re(['code' => 1, 'message' => $data['info'], 'data' => []]);
        }
    }
    // 获取队伍信息
    public function team_info () {
        $param = pd($_POST);
        $user_info = $this->checkLogin();
        //dump($user_info);exit;

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }
        $apiJjy = new JjyApi();

        $data   = $apiJjy->team_info($user_token);
        $this->re(['code' => 1, 'message' => $data['info'], 'data' => $data]);
        
    }

    // 创建队伍
    public function create_team () {
        $param = pd($_POST);
        $user_info = $this->checkLogin();
        //dump($user_info);exit;

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }
        $apiJjy = new JjyApi();

        $data   = $apiJjy->create_team($user_token);
        $this->re(['code' => 1, 'message' => $data['info'], 'data' => $data]);
        
    }

    // 加入队伍
    public function join_team () {
        $param = pd($_POST);
        $user_info = $this->checkLogin();
        //dump($user_info);exit;

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
            $team_name = $param['team_name'];
        }
        $apiJjy = new JjyApi();

        $data   = $apiJjy->join_team ($user_token,$team_name);
        $this->re(['code' => 1, 'message' => $data['info'], 'data' => $data]);
        
    }

    // 退出队伍
    public function quit_team () {
        $param = pd($_POST);
        $user_info = $this->checkLogin();
        //dump($user_info);exit;

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }
        $apiJjy = new JjyApi();

        $data   = $apiJjy->quit_team ($user_token);
        $this->re(['code' => 1, 'message' => $data['info'], 'data' => $data]);
        
    }

    // 解散队伍
    public function del_team () {
        $param = pd($_POST);
        $user_info = $this->checkLogin();
        //dump($user_info);exit;

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }
        $apiJjy = new JjyApi();

        $data   = $apiJjy->del_team ($user_token);
        $this->re(['code' => 1, 'message' => $data['info'], 'data' => $data]);
        
    }
    // 踢除队员
    public function kick_off () {
        $param = pd($_POST);
        $user_info = $this->checkLogin();
        //dump($user_info);exit;

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
            $uid = $param['uid'];
        }
        $apiJjy = new JjyApi();

        $data   = $apiJjy->kick_off ($user_token,$uid);
        $this->re(['code' => 1, 'message' => $data['info'], 'data' => $data]);
        
    }
}
