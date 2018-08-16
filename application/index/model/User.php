<?php
//命名空间
namespace app\admin\model;

//定义类
class User extends Common
{
    //条件判断
    public $add_rule = [
        'name|登录名'    => 'unique:user|require|max:64', // unique后加数据表(不带前缀)
        'password|密码' => 'require|max:32', // unique后加数据表(不带前缀)

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
     //登陆验证
    public function login($idcard, $password)
    {
        //寻找用户名对应的那条记录
        $where = array('idcard' => $idcard);
        $info  = $this->where($where)->find();
        if ($info) {
            //密码加密
            $salt     = config('password_pre');
            $password = md5(md5($password) . $salt);
            //判断密码是否正确
            if ($password == $info['password']) {
                //把id，和usernam存到session中
                session('id', $info['id']);
                session('username', $info['username']);
                //把用户对应得角色的权限取出并放入session中
                
                return 3;
            } else {
                return 2;
            }
        } else {
            return 1;
        }
    }

}
