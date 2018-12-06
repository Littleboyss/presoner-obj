<?php
//命名空间
namespace app\index\Controller;

//定义类
class User extends Main
{
    //显示添加界面
    public function add()
    {
        $User = model('User');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            if (!preg_match("/^1[34578][0-9]{9}$/", $post_data['phone'])) {
                $this->returnMsg(3, '请正确输入手机号'); // 请正确输入手机号
            }
            $post_data['hospital_id'] = session('id');
            $post_data['addtime']     = time();
            $post_data['password']    = '123456';
            $error                    = $User->check($post_data);
            if (!$error) {
                $User->_before_insert($post_data);
                $res = $User->save($post_data);
                if ($res) {
                    $this->returnMsg(0, '添加成功');
                    exit;
                }
            }
            $this->returnMsg(1, '添加失败' . $error);
            exit;
        }
        return $this->fetch();
    }
    // 患者列表页
    public function index()
    {
        $UserModel = model('User');
        $Hospital  = model('Hospital');
        $Experts   = model('Experts');
        $data      = $UserModel->paginate(10);
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

    // 查看病历
    public function viewHistory()
    {

        $id               = $this->request->param('id');
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
        return $this->fetch('Hospital/histroy');
    }
    // 添加病例
    public function addHistory()
    {
        $User = model("UserHistory");
        if ($this->request->isPost()) {
            $post_data            = $this->request->post();
            $error                = $User->check($post_data);
            $post_data['vtime']   = strtotime($post_data['vtime']);
            $post_data['addtime'] = time();
            if (!$error) {
                $res = $User->save($post_data);
                if ($res) {
                    $this->returnMsg(0, '添加成功');
                    exit;
                }
            }
            $this->returnMsg(1, '添加失败' . $error);
            exit;
        }
    }
}
