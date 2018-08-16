<?php
//命名空间
namespace app\user\model;

use app\common\util\Verify;

//定义类
class Admin extends Common
{

    public $add_rule = [
        'username|管理员名称' => 'unique:admin|require|max:64', // unique后加数据表(不带前缀)
        'password|密码'    => 'require|max:20',
    ];
    public $edit_rule = [
        'username|管理员名称' => 'require|max:64', // unique后加数据表(不带前缀)
        'password|密码'    => 'require',
    ];
    //验证码验证
    public function _checkCode($code)
    {
        // 验证码验证
        $verify = new Verify();
        return $verify->check($code);
    }
    //插入数据时给密码进行加密
    public function _before_insert(&$data)
    {
        $salt             = config('password_pre');
        $data['password'] = md5(md5($data['password']) . $salt);
        $data['addtime']  = time();
    }
    //插入数据时给密码进行加密
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
    public function login($username, $password)
    {
        //寻找用户名对应的那条记录
        $where = array('username' => $username);
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
                $role_id = $info['role_id'];
                $this->_getAuthByUserId($role_id);
                // 更新登陆时间 次数 ip
                $save_data['loginip']    = getClientIp(0, true);
                $save_data['logintime']  = time();
                $save_data['logintimes'] = $info['logintimes'] + 1;
                $this->where('id =' . $info['id'])->update($save_data);
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
                $this->_getAuthByUserId($info['role_id']);
                session('username', $info['username']);
            }
        }
    }
    // 把用户的角色的权限取出并放入session中
    public function _getAuthByUserId($role_id)
    {
        $authModel = model('auth');
        $roleModel = model('role');
        //找到对应的权限role_id_list
        $auth_list = $roleModel->field('auth_list')->find($role_id);
        $auth_list = $auth_list['auth_list'];
        //拥有所有权限
        $auth = $arrayName = array(); //保存控制器下的方法
        if ($auth_list == '*') {
            //保存控制器下的方法
            //获取对应的manu
            $_auth = $authModel->select();
        } else {
            //一般用户登录
            $_auth = $authModel->where("id in ({$auth_list})")->select();
        }
        foreach ($_auth as $v) {
            //保存控制器下的方法
            $auth[] = $v['auth_c'] . '/' . $v['auth_a'];
        }
        //获取对应的manu
        $manu = $_auth;
        session('auth', $auth);
        session('manu', $manu);

    }

}
