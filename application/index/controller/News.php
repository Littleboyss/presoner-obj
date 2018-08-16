<?php
//命名空间
namespace app\user\Controller;

//定义类
class News extends \think\Controller
{
    // 显示
    public function show(){
        $id        = $this->request->param('id');
        $NewsModel = model("News");
        $data     = $NewsModel->find($id);
        $data['catename'] = model('Category')->where('catid =' . $data['catid'])->value('catename');
        $this->assign('data', $data);
        return $this->fetch();
    }
}
