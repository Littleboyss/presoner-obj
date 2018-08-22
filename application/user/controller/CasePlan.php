<?php
//命名空间
namespace app\user\Controller;

//定义类
class CasePlan extends Main
{
    //显示添加界面
    public function add()
    {
        $uid            = $this->request->param('uid');
        $CasePlan = model('CasePlan');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $post_data['uid'] = $uid;
            $error     = $CasePlan->check($post_data);
            if (!$error) {
                $CasePlan->_before_insert($post_data);
                $res = $CasePlan->save($post_data);
                if ($res) {
                    $this->success('添加成功');
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
        $CasePlanModel = model('CasePlan');
        $data          = $CasePlanModel->order('stime desc')->paginate(10);
        foreach ($data as $key => $value) {
            $data[$key]['name'] = model('User')->where('id ='.$value['uid'])->value('name');
        }
        $page          = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function edit()
    {
        $id            = $this->request->param('id');
        $CasePlanModel = model("CasePlan");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $CasePlanModel->check($post_data, 2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $CasePlanModel->_before_update($post_data);
            $res = $CasePlanModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('修改成功', 'index');
                exit();
            } else {
                $this->error('修改失败' . $CasePlanModel->getError());
            }
        }
        $CasePlanInfo = $CasePlanModel->find($id);
        $this->assign('CasePlanInfo', $CasePlanInfo);
        return $this->fetch();
    }

    // 删除
    public function del()
    {
        $id            = $this->request->param('id');
        $CasePlanModel = model('CasePlan');
        if ($CasePlanModel->where('id =' . $id)->delete()) {
            $this->return_msg(0, '删除成功');
        } else {
            $this->return_msg(1, '删除失败');
        }
    }
}
