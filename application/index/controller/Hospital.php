<?php
//命名空间
namespace app\index\Controller;

//定义类
class Hospital extends Main
{
    public function edit()
    {
        $id            = session('id');
        $HospitalModel = model("Hospital");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $HospitalModel->check($post_data, 2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $res = $HospitalModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败' . $HospitalModel->getError());
            }
        }
    }


    // 详情页
    public function detail()
    {
        $UserModel = model('User');
        $Hospital  = model('Hospital');
        $Experts   = model('Experts');
        $data      = $UserModel->where(['hospital_id'=>session('id')])->paginate(10);
        $hospital_info = $Hospital->where('id =' . session('id'))->find();
        foreach ($data as $key => $value) {
            if ($value['eid'] == 0) {
                $data[$key]['Experts'] = '暂无';
            } else {
                $data[$key]['Experts'] = $Experts->where('id =' . $value['eid'])->value('name');
            }
        }
        $page = $data->render();
        $this->assign('data', $data);
        $this->assign('hospital_info', $hospital_info);
        $this->assign('page', $page);
        return $this->fetch();
    }
}
