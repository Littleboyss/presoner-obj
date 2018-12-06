<?php
//命名空间
namespace app\experts\Controller;

//定义类
class Experts extends Main
{
    //显示添加界面
    public function add()
    {
        $Experts = model('Experts');
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $Experts->check($post_data);
            if (!$error) {
                $Experts->_before_insert($post_data);
                $res = $Experts->save($post_data);
                if ($res) {
                    $this->success('添加成功', 'index');
                    exit;
                }
            }
            $this->error('添加失败' . $error);
            exit;
        }
        $authModel = model('Auth');
        $data      = $authModel->tree(config('cal_array'));
        $this->assign('cal', $data);
        return $this->fetch();
    }
    public function index()
    {
        $ExpertsModel = model('Experts');
        $authModel = model('Auth');
        $data      = $authModel->tree(config('cal_array'));
        $this->assign('cal', $data);
        $ExpertsInfo = $ExpertsModel->find(session('id'));
        $this->assign('ExpertsInfo', $ExpertsInfo);
        return $this->fetch();
    }
    public function edit()
    {
        $id           = $this->request->param('id');
        $ExpertsModel = model("Experts");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $error     = $ExpertsModel->check($post_data, 2);
            if ($error) {
                $this->error('修改失败' . $error);
                exit;
            }
            $ExpertsModel->_before_update($post_data);
            $res = $ExpertsModel->save($post_data, ['id' => $id]);
            if ($res) {
                $this->success('修改成功', 'index');
                exit();
            } else {
                $this->error('修改失败' . $ExpertsModel->getError());
            }
        }
    }

    // 删除
    public function del()
    {
        $id           = $this->request->param('id');
        $ExpertsModel = model('Experts');
        if ($ExpertsModel->where('id =' . $id)->delete()) {
            $this->return_msg(0, '删除成功');
        } else {
            $this->return_msg(1, '删除失败');
        }
    }
    // 查看排班表
    public function showSchedule()
    {
        $id            = $this->request->param('id');
        $ScheduleModel = model("Schedule");
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $res       = $ScheduleModel->where(['eid' => $id])->order('weeks asc')->select()->toArray();
            if ($res) {
                foreach ($res as $key => $value) {
                    $res[$key]['weeks'] = getWeeks($value['weeks']);
                }
                $this->returnMsg(0, '获取成功', $res);
            }
        }
        $this->returnMsg(1, '获取失败');
    }
    // 显示选择专家界面
    public function selectEid()
    {
        $uid           = $this->request->param('uid');
        $User = model('User');
        $ExpertsModel = model("Experts");
        $ScheduleModel = model("Schedule");
        $eid = $User->where(['id'=>$uid])->value('eid');
        $this->assign('eid',$eid);
        $where = [];
        $this->assign('map', ['specialty'=>0,'weeks'=>0,]);
        if ($this->request->isPost()) {
            $post_data = $this->request->post();
            $where = $post_data;
            // 特长查询分类下的科
            if ($where['specialty'] != 0) {
                $authModel = model('Auth');
                $cal_datas      = $authModel->tree(config('cal_array'),$where['specialty']);
                $specialty_id=[];
                array_unshift($specialty_id,$where['specialty']);
                if ($cal_datas) {
                    $specialty_id = array_column($cal_datas, 'id');
                    $specialty_ids = implode(',', $specialty_id);
                    $where['specialty'] = ['in',$specialty_ids];
                }
            }else{
                unset($where['specialty']);
            }
            // 排班
            if($where['weeks'] != 0){    
                $list = $ScheduleModel->where(['weeks'=>$where['weeks']])->group('eid')->column('eid');
                if ($list) {
                    $in_data = implode(',', $list);               
                    $where['id'] = ['in',$list];
                }else{
                    $where['id'] = 0;
                }
            }
            unset($where['weeks']);
            $this->assign('map', $post_data);
        }
        $data  = $ExpertsModel->where($where)->paginate(10);
        $page  = $data->render();
        $weeks = [1 => '星期一', 2 => '星期二', 3 => '星期三', 4 => '星期四', 5 => '星期五', 6 => '星期六', 7 => '星期天'];
        $authModel = model('Auth');
        $this->assign('cal',config('cal_array'));
        $this->assign('data', $data);
        $this->assign('uid', $uid);
        $this->assign('weeks', $weeks);
        $this->assign('page', $page);
        return $this->fetch();
    }
    // 完成专家选择
    public function choice()
    {
        $User = model('User');
        $id        = $this->request->param('uid');
        $eid        = $this->request->param('eid');
        //dump($id);dump($eid);exit;
        $res = $User->save(['eid'=>$eid], ['id' => $id]);
        if($res){
            $this->returnMsg(0,'完成专家选择');
        }else{
            $this->returnMsg(1,'专家选择失败');
        }
    }
}
