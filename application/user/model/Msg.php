<?php
//命名空间
namespace app\user\model;

//定义类
class Msg extends Common
{
    //条件判断
    public $add_rule = [
        'content|内容'    => 'require|max:2000', 
    ];

}
