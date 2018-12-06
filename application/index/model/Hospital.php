<?php
//命名空间
namespace app\index\model;

//定义类
class Hospital extends Common
{
    //条件判断
    public $add_rule = [
        'name|登录名'    => 'unique:hospital|require|max:64', // unique后加数据表(不带前缀)
        'password|密码' => 'require|max:32', // unique后加数据表(不带前缀)

    ];
    public $edit_rule = [
        'nickname|名称'   => 'require|max:64',
        'descrip|描述'   => 'require',
    ];


     //登陆验证
    public function login($name, $password)
    {
        //寻找用户名对应的那条记录
        $where = array('name' => $name);
        $info  = $this->where($where)->find();
        if ($info) {
            //密码加密
            $salt     = config('password_pre');
            $password = md5(md5($password) . $salt);
            //判断密码是否正确
            if ($password == $info['password']) {
                //把id，和usernam存到session中
                session('id', $info['id']);
                session('name', $info['name']);
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
