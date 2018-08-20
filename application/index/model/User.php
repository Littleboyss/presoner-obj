<?php
//命名空间
namespace app\index\model;

//定义类
class User extends Common
{
    //条件判断
    public $add_rule = [
        'name|登录名'    => 'unique:user|require|max:64', // unique后加数据表(不带前缀)
        'password|密码' => 'require|max:32', 
        'phone|手机号' => 'require|max:11', 
        'idcard|身份证' => 'require|max:18', 
        'sex|性别' => 'require|number|in:1,2',
        'age|年龄' => 'require|number|between:1,100',
        'item|服务条款'=>'require'
    ];
    public $edit_rule = [
        'name|权限名称' => 'require|max:64',
    ];

    //插入数据时给密码进行加密
    public function _before_insert(&$data)
    {
        $salt             = config('password_pre');
        $data['password'] = md5(md5($data['password']) . $salt);
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
    public function setCookies()
    {
        //判断是否有cookie
        if (cookie('id') != '') {
            $id       = cookie('id');
            $password = cookie('password');
            $info     = $this->find($id);
            //判断密码是否正确
            if ($info['password'] == $password) {
                //设置session
                session('id', $info['id']);
                $this->_getAuthByUserId($info['role_id']);
                session('username', $info['username']);
            }
        }
    }

}
