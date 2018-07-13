<?php
//命名空间
namespace app\admin\Controller;

//定义类
class Experts extends Main
{
    //显示添加界面
    public function add()
    {
        $Experts = model('Experts');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $Experts->check($post_data);
            if (!$error) {
                $Experts->_before_insert($post_data);
                $res = $Experts->save($post_data);
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
        $ExpertsModel = model('Experts');
        $data         = $ExpertsModel->paginate(10);
        $page         = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function edit()
    {
        $id           = $this->request->param('id');
        $ExpertsModel = model("Experts");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $ExpertsModel->check($post_data, 2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $ExpertsModel->_before_update($post_data);
            $res = $ExpertsModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('修改成功', 'index');
                exit();
            } else {
                $this->error('修改失败' . $ExpertsModel->getError());
            }
        }
        $ExpertsInfo = $ExpertsModel->find($id);
        $this->assign('ExpertsInfo', $ExpertsInfo);
        return $this->fetch();
    }

    // 删除
    public function del()
    {
        $id           = $this->request->param('id');
        $ExpertsModel = model('Experts');
        if ($ExpertsModel->where('id =' . $id)->delete()) {
            $this->return_msg(0, '删除成功');
        } else {
            $this->return_msg(1, '删除失败');
        }
    }
    // 查看排班表
    public function showSchedule()
    {
        $id           = $this->request->param('id');
        $ScheduleModel = model("Schedule");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $res = $ScheduleModel->where(['eid'=>$id])->order('weeks asc')->select()->toArray();
            if ($res) {
                foreach ($res as $key => $value) {
                    $res[$key]['weeks'] = getWeeks($value['weeks']);
                }
                $this->returnMsg(0,'获取成功',$res);
            }
        }
        $this->returnMsg(1,'获取失败');
    }
}
