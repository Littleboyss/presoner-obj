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
}
