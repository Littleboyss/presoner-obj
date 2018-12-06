<?php
//命名空间
namespace app\user\model;

//定义类
class News extends Common
{
    //条件判断
    public $add_rule = [
        'title|标题'    => 'require|max:255', 
        'introduce|新闻简介' => 'require', 
        'author|编辑' => 'require', 
    ];
    public $edit_rule = [

    ];
    public function getCategory(){
    	return model('Category')->select()->toArray();
    }
    public function _before_insert(&$data){
        $data['author'] = session('username');
        $data['add_time'] = time();
        if ($data['is_jump_link'] == 2) {
            unset($data['link']);
        }
        return;
    }
    public function _before_update(&$data){
        $data['modif_time'] = time();
        if ($data['is_jump_link'] == 2) {
            unset($data['link']);
        }
        return;
    }

}
