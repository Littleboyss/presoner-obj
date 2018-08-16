<?php
//命名空间
namespace app\user\Controller;

//定义类
class User extends Main
{
    //显示添加界面
    public function add()
    {
        $User = model('User');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $User->check($post_data);
            if (!$error) {
                $User->_before_insert($post_data);
                $res = $User->save($post_data);
                if ($res) {
                    $this->success('添加成功', 'index');
                    exit;
                }
            }
            $this->error('添加失败' . $error);
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
    // 患者信息修改
    public function edit()
    {
        $id        = $this->request->param('id');
        $UserModel = model("User");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $UserModel->check($post_data, 2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $UserModel->_before_update($post_data);
            $res = $UserModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('修改成功', 'index');
                exit();
            } else {
                $this->error('修改失败' . $UserModel->getError());
            }
        }
        $UserInfo = $UserModel->find($id);
        $this->assign('UserInfo', $UserInfo);
        return $this->fetch();
    }

    // 删除
    public function del()
    {
        $id        = $this->request->param('id');
        $UserModel = model('User');
        if ($UserModel->where('id =' . $id)->delete()) {
            $this->returnMsg(0, '删除成功');
        } else {
            $this->returnMsg(1, '删除失败');
        }
    }

    // 查看病历
    public function viewHistory()
    {
        $id               = $this->request->param('id');
        $UserModel        = model("User");
        $UserHistoryModel = model("UserHistory");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $res       = $UserHistoryModel->where(['uid' => $id])->order('addtime desc')->select()->toArray();
            if ($res) {
                foreach ($res as $key => $value) {
                    $res[$key]['vtime']   = date('y-m-d', $value['vtime']);
                    $res[$key]['addtime'] = date('Y-m-d', $value['addtime']);
                }
                $this->returnMsg(0, '获取成功', $res);
            }
        }
        $this->returnMsg(1, '获取失败');
    }
    // 治疗管理
    public function getEid()
    {
        $UserModel = model('User');
        $Hospital  = model('Hospital');
        $Experts   = model('Experts');
        $where = [];
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $where['uid'] = $post_data['uid'];
            $where['name'] = ['like'=>$post_data['name']];
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
        $data      = $model->order('addtime desc')->paginate(10);
        foreach ($data as $key => $value) {
            $data[$key]['name'] = $UserModel->where(['id' => $value['uid']])->value('name');
        }
        $page = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
     // 服药期间不适患者列表
    public function test()
    {
        $user = model("Test");
        $list = [
            ['id'=>1, 'name'=>'thinkphp'],
            ['id'=>2, 'name'=>'onethink']
        ];
        $res = $user->saveAll($list);
        dump($res);
    }
}
