<?php
//命名空间
namespace app\index\model;

//定义类
class UserHistory extends Common
{
     //条件判断
    public $add_rule = [
        'uid|用户'    => 'require',
        'vtime|就诊时间' => 'require', // unique后加数据表(不带前缀)
        'content|描述' =>'require',
    ];

}
