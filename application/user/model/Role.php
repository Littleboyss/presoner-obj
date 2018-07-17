<?php
//命名空间
namespace app\admin\model;

//引入核心控制器
use think\Model;

//定义类
class Role extends Common
{
    //条件判断
    public $add_rule =  [
        'role_name|权限名称' => 'unique:role|require|max:64', // unique后加数据表(不带前缀)
    ];
    public $edit_rule =  [
        'role_name|权限名称' => 'require|max:64', 
    ];
    public function _before_insert(&$data)
    {
        $data['auth_list'] = implode(',', $data['auth_list']);
    }
    public function _before_update(&$data)
    {
        $data['auth_list'] = implode(',', $data['auth_list']);
    }
    public function idToZh()
    {
        $sql = "select a.*,GROUP_CONCAT(auth_name) auth_name from ph_role a  left join ph_auth b on FIND_IN_SET(b.id,a.auth_list) GROUP BY a.id";
        return $this->query($sql);
    }
}
