<?php
//命名空间
namespace app\admin\model;

//定义类
class CasePlan extends Common
{
    public $add_rule = [
        'uid|患者id'      => 'require', // unique后加数据表(不带前缀)
        'descrip|描述'    => 'require|max:225',
        'hospital|就诊医院' => 'require|max:50',
        'stime|就诊时间'    => 'require',
    ];
    public function _before_insert(&$data)
    {
        //判断密码是否为空
        if ($data['stime']) {
            $data['stime'] = strtotime(($data['stime']));
        } else {
            unset($data['stime']);
        }
        $data['addtime'] = time();
    }

    public function _before_update(&$data)
    {
        //判断密码是否为空
        if ($data['stime']) {
            $data['stime'] = strtotime(($data['stime']));
        } else {
            unset($data['stime']);
        }
    }
}
