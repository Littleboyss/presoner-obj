<?php
//命名空间
namespace app\user\Controller;

//定义类
class User extends Main
{
    // 患者列表页
    public function index()
    {
        $UserModel = model('User');
        $id        = session('id');
        $data      = $UserModel->where(['id' => $id])->find();
        $this->getNowPwd();
        $this->assign('data', $data);
        return $this->fetch('userinfo');
    }
    // 患者信息修改
    public function edit()
    {
        $id        = session('id');
        $UserModel = model("User");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $UserModel->check($post_data, 2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $res = $UserModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('修改成功', 'index');
                exit();
            } else {
                $this->error('修改失败' . $UserModel->getError());
            }
        }
    }
    // 改密
    public function changePassword()
    {
        if ($this->request->isPost()) {
            $UserModel = model('User');
            $id        = session('id');
            $data      = $UserModel->where(['id' => $id])->value('password');
            $post_data = $this->request->post();
            $password  = $post_data['old_password'];
            $salt      = config('password_pre');
            $password  = md5(md5($password) . $salt);
            //判断密码是否正确
            if ($password == $data) {
                $save        = $post_data['password'];
                $salt        = config('password_pre');
                $ch_password = md5(md5($save) . $salt);
                $res         = $UserModel->save(['password' => $ch_password], ['id' => $id]);
                if ($res) {
                    $this->returnMsg(0, '密码修改成功');
                } else {
                    $this->returnMsg(2, '密码修改失败');
                }
            } else {
                $this->returnMsg(1, '原密码不正确');
            }
        }
    }

    // 查看病历
    public function history()
    {
        $id               = session('id');
        $UserModel        = model("User");
        $user_name        = $UserModel->where(['id' => $id])->value('name');
        $UserHistoryModel = model("UserHistory");
        $res              = $UserHistoryModel->where(['uid' => $id])->order('vtime desc')->select()->toArray();
        if ($res) {
            foreach ($res as $key => $value) {
                $res[$key]['vtime']      = date('Y/m/d', $value['vtime']);
                $res[$key]['addtime']    = date('Y/m/d', $value['addtime']);
                $res[$key]['img_banner'] = explode(',', $value['img_banner']);
            }
        }
        $Hospital      = model('Hospital');
        $hospital_info = $Hospital->where('id =' . session('id'))->find();
        $this->assign('hospital_info', $hospital_info);
        $this->assign('data', $res);
        $this->assign('user_name', $user_name);
        return $this->fetch();
    }
    // 治疗管理
    public function getEid()
    {
        $UserModel = model('User');
        $Hospital  = model('Hospital');
        $Experts   = model('Experts');
        $where     = [];
        if ($this->request->isPost()) {
            $post_data     = $this->request->post();
            $where['uid']  = $post_data['uid'];
            $where['name'] = ['like' => $post_data['name']];
        }
        $data = $UserModel->where($where)->order('eid desc')->paginate(10);
        foreach ($data as $key => $value) {
            if ($value['eid'] == 0) {
                $data[$key]['Experts'] = '暂无';
            } else {
                $data[$key]['Experts'] = $Experts->where('id =' . $value['eid'])->value('name');
            }
            $data[$key]['hospital'] = $Hospital->where('id =' . $value['hospital_id'])->value('nickname');
        }
        $page = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    // 重设密码
    public function reset()
    {
        $id = $this->request->param('id');
        if ($this->request->isPost()) {
            $UserModel        = model("User");
            $data['password'] = '123456';
            $UserModel->_before_update($data);
            $res = $UserModel->save($data, ['id' => $id]);
            if ($res) {
                $this->returnMsg(0, '密码重置成功');
            }
        }
        $this->returnMsg(1, '密码重置失败');
    }
    // 患者交流信息
    public function msg()
    {
        $uid  = $this->request->param('uid');
        $Msg  = model('Msg');
        $data = $Msg->where(['uid' => $uid])->select();
        $this->assign('data', $data);
        return $this->fetch();
    }
    // 服药期间不适患者列表
    public function issue()
    {
        $UserModel = model("User");
        $model     = model('UserIssue');
        $data      = $model->where(['uid' => session('id')])->order('addtime desc')->paginate(10);
        foreach ($data as $key => $value) {
            $data[$key]['name'] = $UserModel->where(['id' => $value['uid']])->value('name');
        }
        $page = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    //显示添加界面
    public function issueadd()
    {
        $User = model('UserIssue');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $User->check($post_data);
            if (!$error) {
                $post_data['addtime'] = time();
                $post_data['uid']     = session('id');
                $res                  = $User->save($post_data);
                if ($res) {
                    $this->returnMsg(0, '添加成功');
                }
            }
            $this->returnMsg(1, '添加成功');
            exit;
        }
    }
    // 回访
    public function comeback()
    {
        $id               = session('id');
        $UserModel        = model("User");
        $user_name        = $UserModel->where(['id' => $id])->value('name');
        $UserHistoryModel = model("UserHistory");
        $res              = $UserHistoryModel->where(['uid' => $id])->order('vtime desc')->find()->toArray();
        $flag             = false;
        if ($res) {
            if ($res['vtime'] + 3600 * 24 * 30 < time()) {
                $flag = true;
            }
        }
        $this->assign('flag', $flag);
        return $this->fetch();
    }
    // 治疗计划
    public function plan()
    {
        $CasePlanModel = model('CasePlan');
        $data          = $CasePlanModel->order('stime desc')->where(['uid' => session('id')])->paginate(10);
        $page          = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    // 我的专家
    public function expert()
    {
        $expert_id = model('user')->where(['id' => session('id')])->value('eid');
        $Msg       = model('Msg');
        $data      = $Msg->where(['uid' => session('id'), 'eid' => $expert_id])->select();
        $this->assign('data', $data);
        $ExpertsModel             = model("Experts");
        $ExpertsInfo              = $ExpertsModel->find($expert_id);
        $ExpertsInfo['specialty'] = config('cal')[$ExpertsInfo['specialty']];
        $this->assign('ExpertsInfo', $ExpertsInfo);
        return $this->fetch();
    }
    // 回复
    public function addmsg()
    {
        $Msg = model('msg');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $Msg->check($post_data);
            if (!$error) {
                $post_data['addtime'] = time();
                $post_data['uid']     = session('id');
                $post_data['type']    = 1;
                $res                  = $Msg->save($post_data);
                if ($res) {

                    $this->returnMsg(0, '回复成功',date('m/d H:i',time()));
                }
            }
            $this->returnMsg(1, '回复失败');
            exit;
        }
    }
}
