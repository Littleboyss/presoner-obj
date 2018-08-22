<?php
//命名空间
namespace app\experts\model;

use think\Model;
use think\Validate;

//定义类
class common extends Model
{
    protected $add_rule    = [];
    protected $edit_rule    = [];
    protected $massage = [];
    // 验证
    // $data array 需要验证的数据
    // $type int   默认添加规则验证
    public function check($data,$type=1)
    {
        if ($type == 1) {
            $rule = $this->add_rule;
        }elseif ($type == 2){
            $rule = $this->edit_rule;
        }
        $validate = new Validate($rule, $this->massage);
        $result   = $validate->check($data);
        if (!$result) {
            return $validate->getError();
        } else {
            return false;
        }
    }
}
