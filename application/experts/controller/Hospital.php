<?php
//命名空间
namespace app\admin\Controller;

//定义类
class Hospital extends Main
{
    //显示添加界面
    public function add()
    {
        $Hospital = model('Hospital');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $Hospital->check($post_data);
            if (!$error) {
                $Hospital->_before_insert($post_data);
                $res = $Hospital->save($post_data);
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
        $HospitalModel = model('Hospital');
        $data          = $HospitalModel->paginate(10);
        $page          = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function edit()
    {
        $id            = $this->request->param('id');
        $HospitalModel = model("Hospital");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $HospitalModel->check($post_data, 2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $HospitalModel->_before_update($post_data);
            $res = $HospitalModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('修改成功', 'index');
                exit();
            } else {
                $this->error('修改失败' . $HospitalModel->getError());
            }
        }
        $HospitalInfo = $HospitalModel->find($id);
        $this->assign('HospitalInfo', $HospitalInfo);
        return $this->fetch();
    }

    // 删除
    public function del()
    {
        $id            = $this->request->param('id');
        $HospitalModel = model('Hospital');
        if ($HospitalModel->where('id =' . $id)->delete()) {
            $this->return_msg(0, '删除成功');
        } else {
            $this->return_msg(1, '删除失败');
        }
    }
}
