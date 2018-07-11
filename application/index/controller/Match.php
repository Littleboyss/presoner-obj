<?php
namespace app\index\controller;

use app\common\util\JjyApi;
use app\common\util\SteamauthOOP;
use think\View;

class Match extends Main {
    // 比赛列表
    public function matchlist() {
        $param = pd($_GET);
        $type = isset($param['type']) && !empty($param['type']) ? $param['type'] : 3;
        $mode = isset($param['mode']) && !empty($param['mode']) ? $param['mode'] : 1;

        $user_info = $this->checkLogin(false);

        if (empty($user_info)) {
            $user_token = '';
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy    = new JjyApi();

        $data      = $apiJjy->getMatchList ($user_token, $type, $mode);

        if ($data['status'] === 0) {
            $list = $data['data'];
        } else {
            $list = [];
        }

        $view = new View();
        $view->assign('type', $type);
        $view->assign('mode', $mode);
        $view->assign('list', $list);
        return $view->fetch('match/matchlist');
    }

    // 比赛详情
    public function matchdetail () {
        $match_id = p('match_id');

        $user_info = $this->checkLogin(false);

        if (empty($user_info)) {
            $user_token = '';
            $steam_user = '';
        } else {
            $user_token = $user_info['token'];
            $steam_user = $user_info['steamuser'];
        }

        $apiJjy    = new JjyApi();

        $data      = $apiJjy->getMatchDetail ($user_token, $match_id);

        if (intval($data['status']) == 2) {
            header("Location: " . config('LOCAL_URL') . '/index.php/match/matchlist');
            exit;
        }

        $steam = new SteamauthOOP();

        $steam->setReturnUrl ($user_token);

        $view = new View();
        $view->assign('detail',    $data['data']);
        $view->assign('steamuser', $steam_user);
        $view->assign('steam',     $steam);
        return $view->fetch('match/matchdetail');
    }

    // 获取比赛排行
    public function getmatchrank () {
        $match_id = p('match_id');

        $user_info = $this->checkLogin(false);

        if (empty($user_info)) {
            $user_token = '';
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy    = new JjyApi();

        $data      = $apiJjy->userRank ($user_token, $match_id);

        if ($data['status'] === 0) {
            $list = $data['data'];
            if (isset($data['extra_data']) && !empty($data['extra_data'])) {
                $user = $data['extra_data'];
            } else {
                $user = [];
            }
        } else {
            $list = [];
            $user = [];
        }

        $this->re(['code' => 1, 'message' => '', 'data' => ['list' => $list, 'user' => $user]]);
    }

    // 获取比赛记录
    public function getmatchrecord () {
        $match_id = p('match_id');

        $user_info = $this->checkLogin(false);

        if (empty($user_info)) {
            $user_token = '';
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy    = new JjyApi();

        $data      = $apiJjy->userRecord ($user_token, $match_id);

        if ($data['status'] === 0) {
            $list = $data['data'];

            foreach ($list as $key => $val) {
                $val['add_time']   = date('Y-m-d H:i', $val['add_time']);
                $val['start_time'] = date('Y-m-d H:i', $val['start_time']);
                $list[$key]        = $val;
            }
        } else {
            $list = [];
        }

        $this->re(['code' => 1, 'message' => '', 'data' => $list]);
    }

    // 比赛报名
    public function sign () {
        $match_id = p('match_id');

        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy = new JjyApi();

        $data   = $apiJjy->matchSign ($user_token, $match_id);

        switch (intval($data['status'])) {
            case 200 : $return = ['code' => $data['status'], 'msg' => '', 'data' => $data['data']]; break;
            default  : $return = ['code' => $data['status'], 'msg' => $data['info'], 'data' => []]; break;
        }

        echo json_encode($return, JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 校验订单
    public function checkorder () {
        $order_no  = p('orderno');

        $user_info = $this->checkLogin();

        if (empty($user_info)) {
            $this->re(['code' => 0, 'message' => '您尚未登录，请登录再操作', 'data' => ['nologin' => 1]]);
        } else {
            $user_token = $user_info['token'];
        }

        $apiJjy = new JjyApi();

        $data   = $apiJjy->userHaspay ($user_token, $order_no);

        if (intval($data['status']) !== 0) {
            $this->re(['code' => 0, 'message' => $data['info'], 'data' => []]);
        } else {
            $this->re(['code' => 1, 'message' => $data['info'], 'data' => []]);
        }
    }
}
