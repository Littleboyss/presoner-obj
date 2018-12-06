<?php
//命名空间
namespace app\admin\Controller;

//定义类
class Category extends Main
{
    //显示添加界面
    public function add()
    {
        $Category = model('Category');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $Category->check($post_data);
            if (!$error) {
                $Category->_before_insert($post_data);
                $res = $Category->save($post_data);
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
        $CategoryModel = model('Category');
        $data          = $CategoryModel->paginate(10);
        foreach ($data as $key => $value) {
            $data[$key]['catename'] = model('Category')->where('id ='.$value['catid'])->value('catename');
        }
        $page          = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function edit()
    {
        $id            = $this->request->param('id');
        $CategoryModel = model("Category");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $CategoryModel->check($post_data, 2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $CategoryModel->_before_update($post_data);
            $res = $CategoryModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('修改成功', 'index');
                exit();
            } else {
                $this->error('修改失败' . $CategoryModel->getError());
            }
        }
        $CategoryInfo = $CategoryModel->find($id);
        $this->assign('CategoryInfo', $CategoryInfo);
        return $this->fetch();
    }

    // 删除
    public function del()
    {
        $id            = $this->request->param('id');
        $CategoryModel = model('Category');
        if ($CategoryModel->where('id =' . $id)->delete()) {
            $this->return_msg(0, '删除成功');
        } else {
            $this->return_msg(1, '删除失败');
        }
    }
}
