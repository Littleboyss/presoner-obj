<?php
//命名空间
namespace app\admin\Controller;

//定义类
class Role extends Main
{
    //显示添加界面
    public function add()
    {
        $role = model('role');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $role->check($post_data);
            if (!$error) {
                $role->_before_insert($post_data);
                $res = $role->save($post_data);
                if ($res) {
                    $this->success('添加成功', 'lst');
                    exit;
                }
            }
            $this->error('添加失败' . $error);
            exit;
        }
        $authModel = model('Auth');
        $authData  = $authModel->getTree();
        $this->assign('authData', $authData);
        return $this->fetch();
    }
    public function lst()
    {
        $RoleModel = model('Role');
        $roleData  = $RoleModel->idToZh();
        $this->assign('roleData', $roleData);
        return $this->fetch();
    }
    public function edt()
    {
        $id        = $this->request->param('id');
        $RoleModel = model("Role");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $RoleModel->check($post_data,2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $RoleModel->_before_update($post_data);
            $res = $RoleModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('更新成功！', 'lst');
                exit();
            } else {
                $this->error('更新失败' . $RoleModel->getError());
            }
        }
        $roleInfo = $RoleModel->find($id);
        $this->assign('roleInfo', $roleInfo);
        $authModel = model('Auth');
        $authData  = $authModel->getTree();
        $this->assign('authData', $authData);
        return $this->fetch();
    }

    //ajax删除
    public function ajaxDel()
    {
        $id        = $this->request->param('id');
        $RoleModel = model('Role');
        $userModel = model('AdminAdmin');
        $data      = $userModel->where("role_id = " . $id)->find();
        if (empty($data)) {
            if ($RoleModel->delete($id)) {
                $this->return_msg(0, '删除成功');
            } else {
                $this->return_msg(1, '删除失败');
            }
        } else {
            $this->return_msg(2, '该角色下还有用户');
        }
    }
}
