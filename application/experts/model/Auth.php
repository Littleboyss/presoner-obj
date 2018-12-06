<?php
//命名空间
namespace app\experts\model;

//引入核心控制器
use think\Model;

//定义类
class Auth extends Common
{
    public $add_rule = [
        'auth_name|权限名称' => 'unique:auth|require|max:64', // unique后加数据表(不带前缀)
        'auth_c|控制器'     => 'require|max:20',
        'auth_a|方法'      => 'require|max:20',
        'pid|父类id'       => 'require',
    ];
    public $edit_rule = [
        'id'             => 'require',
    ];
    //判断叶子节点
    public function isLeafNode($id)
    {
        if ($this->getTree($id)) {
            return false;
        } else {
            return true;
        }
    }
    //获取无限极分类后的数据
    public function getTree($id = 0)
    {
        $data = $this->select()->toArray();
        return $this->tree($data, $id);
    }

    /**
     *封装函数，实现无限级分类功能
     *@param1 array  $data，  要遍历的二维数组
     *@param2 int    $pid，   默认的pid值
     *@param3 int    $level， 数据代表的层级
     *@return array  实现分类的二维数组
     */
    public function tree($data, $pid = 0, $level = 0)
    {
        //为了让$tree的值不每次循环清零，把它设为静态变量
        static $tree = array();
        //遍历$data数组
        foreach ($data as $row) {
            //判断第一次循环获取$row['pid']=0的值，之后的循环为本级pid=上一级id
            if ($row['pid'] == $pid) {
                //几录当前的层级数
                $row['level'] = $level;
                //存储当前取得的值
                $tree[] = $row;
                //递归调用，
                $this->tree($data, $row['id'], $level + 1);
            }
        }
        // 返回实现分类的二维数组
        return $tree;
    }
}
