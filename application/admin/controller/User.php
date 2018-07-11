<?php
//命名空间
namespace app\admin\Controller;

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
    public function index()
    {
        $UserModel = model('User');
        $data          = $UserModel->paginate(10);
        $page          = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function edit()
    {
        $id            = $this->request->param('id');
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
        $id            = $this->request->param('id');
        $UserModel = model('User');
        if ($UserModel->where('id =' . $id)->delete()) {
            $this->return_msg(0, '删除成功');
        } else {
            $this->return_msg(1, '删除失败');
        }
    }
}
