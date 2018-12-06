<?php
//命名空间
namespace app\experts\model;

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

    
    //登陆验证
    public function login($username, $password)
    {
        //寻找用户名对应的那条记录
        $where = array('name' => $username);
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
                return 3;
            } else {
                return 2;
            }
        } else {
            return 1;
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
                session('name', $info['name']);
            }
        }
    }

}
