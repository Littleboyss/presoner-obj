<?php
//命名空间
namespace app\admin\controller;

use think\Request;
use think\View;

//定义类
class Admin extends Main
{
    //显示首页
    public function index()
    {
        $view = new View();
        return $this->fetch();
    }
    // 管理员管理
    public function adminadmin()
    {
        $admin = model('Admin');
        $data  = $admin->paginate(10);
        $page  = $data->render();
        foreach ($data as $key => $value) {
            if ($value['role_id']) {
                $data[$key]['role_name'] = model('Role')->where('id =' . $value['role_id'])->column('role_name');
            }
        }
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    // 修改管理员
    public function edit()
    {
        $id = $this->request->param('id');
        if ($id == 1) {
            $this->error('超级管理员无法修改');
            exit;
        }
        $MatchModel = model('Admin');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $MatchModel->check($post_data, 2);
            if (!$error) {
                $MatchModel->_before_update($post_data);
                $res = $MatchModel->save($post_data, ['id' => $id]);
                if ($res) {
                    $this->success('修改成功正在跳转', url('adminadmin'));
                    exit;
                } else {
                    $this->error('修改失败请重新修改' . $MatchModel->getError());
                    exit;
                }
            } else {
                $this->error($error);
            }
        }
        $data     = $MatchModel->where('id =' . $id)->find();
        $roleData = model('Role')->select()->toArray();

        $this->assign('data', $data);
        $this->assign('roleData', $roleData);
        return $this->fetch();
    }
    // 添加管理员
    public function add()
    {
        $MatchModel = model('Admin');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $MatchModel->check($post_data);
            if (!$error) {
                $MatchModel->_before_insert($post_data);
                $MatchModel->data($post_data);
                if ($MatchModel->save()) {
                    $this->success('添加成功正在跳转', Url('adminadmin'));
                    exit;
                } else {
                    $this->error('添加失败请重新修改');
                    exit;
                }
            } else {
                $this->error($error);
            }
        }
        $role     = model('Role');
        $roleData = $role->select()->toArray();
        $this->assign('roleData', $roleData);
        return $this->fetch();
    }
    // 上传图像
    public function upload()
    {

        $file = request()->file('file');
        $path = '../user/images/';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(['size' => 2048000, 'ext' => 'jpg,png,gif'])->move($path);
        if ($info) {
            echo json_encode(array('error' => 0, 'url'=>config('IMG_DOMAIN') . '/images/' . $info->getSaveName()));
        } else {
            // 上传失败获取错误信息
            json_encode(array('error'=>1,'message'=>$file->getError()));
        }
        exit;

    }
    //ajax删除
    public function ajaxDel()
    {
        $id = I('id');
        if ($id > 1) {
            $authModel = D('AdminAdmin');
            if ($authModel->delete($id)) {
                $this->return_msg(0, '删除成功');
            } else {
                $this->return_msg(1, '删除失败');
            }

        }
    }
    // 系统操作
    public function system()
    {
        if (IS_POST) {
            if (Db::table('System')->create()) {
                if (Db::table('System')->where('id = 1')->save()) {
                    $this->success('修改成功正在跳转', U('admin/admin/system'));
                    exit;
                } else {
                    $this->error('修改失败请重新修改');
                    exit;
                }
            }
        } else {
            $data = Db::table('System')->where('id = 1')->find();
            $this->assign('data', $data);
            $this->show();
        }
    }
}
