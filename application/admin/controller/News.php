<?php
//命名空间
namespace app\admin\Controller;

//定义类
class News extends Main
{
    //显示添加界面
    public function add()
    {
        $News = model('News');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $News->_before_insert($post_data);
            $error = $News->check($post_data);
            if (!$error) {
                $res = $News->save($post_data);
                if ($res) {
                    $this->success('添加成功', 'index');
                    exit;
                }
            }
            $this->error('添加失败' . $error);
            exit;
        }
        $category = $News->getCategory();
        $this->assign('category', $category);
        return $this->fetch();
    }
    public function index()
    {
        $NewsModel = model('News');
        $data      = $NewsModel->paginate(10);
        foreach ($data as $key => $value) {
            $data[$key]['catename'] = model('Category')->where('catid =' . $value['catid'])->value('catename');
        }
        $page = $data->render();
        // dump($page);exit;
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function edit()
    {
        $id        = $this->request->param('id');
        $NewsModel = model("News");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $NewsModel->check($post_data, 2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $NewsModel->_before_update($post_data);
            $res = $NewsModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('修改成功', 'index');
                exit();
            } else {
                $this->error('修改失败' . $NewsModel->getError());
            }
        }
        $category = $NewsModel->getCategory();
        $data     = $NewsModel->find($id);
        $this->assign('category', $category);
        $this->assign('data', $data);
        return $this->fetch();
    }

    // 删除
    public function del()
    {
        $id        = $this->request->param('id');
        $NewsModel = model('News');
        if ($NewsModel->where('id =' . $id)->delete()) {
            $this->return_msg(0, '删除成功');
        } else {
            $this->return_msg(1, '删除失败');
        }
    }
    //分类添加
    public function Categoryadd()
    {
        $Category = model('Category');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $Category->check($post_data);
            if (!$error) {
                $res = $Category->save($post_data);
                if ($res) {
                    $this->success('添加成功');
                    exit;
                }
            }
            $this->error('添加失败' . $error);
            exit;
        }
    }
    // 分类列表
    public function Category()
    {
        $CategoryModel = model('Category');
        $data          = $CategoryModel->paginate(10);
        $page          = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    // 分类修改
    public function Categoryedit()
    {
        $id            = $this->request->param('catid');
        $CategoryModel = model("Category");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $CategoryModel->check($post_data, 2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $res = $CategoryModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('修改成功');
                exit();
            } else {
                $this->error('修改失败' . $CategoryModel->getError());
            }
        }
    }
    // 分类删除
    public function show(){
        $id        = $this->request->param('id');
        $NewsModel = model("News");
        $data     = $NewsModel->find($id);
        $this->assign('category', $category);
        $this->assign('data', $data);
        return $this->fetch();
    }
}
