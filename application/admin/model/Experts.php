<?php
//命名空间
namespace app\admin\model;

//定义类
class Experts extends Common
{
    //条件判断
    public $add_rule = [
        'name|登录名'    => 'unique:experts|require|max:20', // unique后加数据表(不带前缀)
        'specialty|特长' => 'require|max:50', 
        'sex|性别' => 'require', 
    ];
    public $edit_rule = [
    ];

    //插入数据时给密码进行加密
    public function _before_insert(&$data)
    {
        $salt             = config('password_pre');
        $data['password'] = md5(md5('123456') . $salt);
        $data['addtime']  = time();
    }

    //修改数据时给密码进行加密
    public function _before_update(&$data)
    {
        //判断密码是否为空
        if ($data['password']) {
            $salt             = config('password_pre');
            $data['password'] = md5(md5($data['password']) . $salt);
        } else {
            unset($data['password']);
        }
    }

}
