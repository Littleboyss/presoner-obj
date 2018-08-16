<?php
//命名空间
namespace app\user\model;

//引入核心控制器
use think\Model;

//定义类
class Category extends Common
{
    //条件判断
    public $add_rule =  [
        'catename|分类名称' => 'unique:category|require|max:30', // unique后加数据表(不带前缀)
    ];
    public $edit_rule =  [
        'catename|分类名称' => 'require|max:30', 
    ];
  
}
