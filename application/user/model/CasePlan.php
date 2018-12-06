<?php
//命名空间
namespace app\user\model;

//定义类
class CasePlan extends Common
{
   
    public function _before_insert(&$data)
    {
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
