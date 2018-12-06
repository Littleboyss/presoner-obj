<?php
//命名空间
namespace app\user\model;

//定义类
class UserIssue extends Common
{
    //条件判断
    public $add_rule = [
        'issue|描述'    => 'require|max:64', // unique后加数据表(不带前缀)
    ];

}
