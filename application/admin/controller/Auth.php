<?php
//命名空间
namespace app\admin\Controller;
//定义类
class Auth extends Main{
	//显示添加界面
	public function add(){
		$authModel=model('Auth');
		if ($this->request->isPost()) {
			$post_data = $this->request->post();
            $error = $authModel->check($post_data);
            if (!$error) {
            	$res = $authModel->save($post_data);
				if($res){
					$this->success('添加成功','lst');
					exit;
				}
            }
			$this->error('添加失败'.$error);
			exit;
		}
		$data = $authModel->getTree();
		$this->assign('data',$data);
		return $this->fetch();
	}
	// 权限列表
	public function lst(){
		$authModel=model('Auth');
		$data=$authModel->getTree();
		$this->assign('data',$data);
		return $this->fetch();
	}
	// 权限修改
	public function edt(){
		$id = $this->request->param('id');
		$authModel = model("auth");
		if($this->request->isPost()){
			$post_data = $this->request->post();
			$error = $authModel->check($post_data,2);
			if ($error) {
				$this->error('修改失败'.$error);
				exit;
			}
			//指定的父级不能是自身
			if($post_data['pid'] == $post_data['id'] ){
				$this->error('自己不能做自己的子级');
				return false;
			}
			//指定的父级不能是自己的子级
			$sublist=$authModel->getTree($post_data['id']);  //当前元素的子级
			foreach($sublist as $rows){
				if($rows['id']==$post_data['pid']){
					$this->error('指定的父级不能是自己的子级');
					return false;
				}
			}
			$res = $authModel->save($post_data, ['id' => $id]);
			if($res){
				$this->success('更新成功！','lst');
				exit();
			}else{
				$this->error('更新失败' . $authModel->getError());
			}
		}
		$authInfo = $authModel->find($id);
		$this->assign('authInfo', $authInfo);
		//显示上级权限
		$data=$authModel->getTree();
		$this->assign('data',$data);
		return $this->fetch();
	}
	// 删除
	public function del(){
		$id=I('id');
		if($id>1){
			$authModel=model('Auth');
			$authModel->delete($id);
		}
		$this->success('删除成功', U('lst'));
		exit;
	}
	//ajax删除
	public function ajaxDel(){
		$id=I('id');
		if($id>=1){
			$authModel=model('Auth');
			if( $authModel->isLeafNode($id)){
				if($authModel->delete($id) ){
					$this->return_msg(0,'删除成功');
				}else{
					$this->return_msg(1,'删除失败');
				}
			}else{
				$this->return_msg(2,'该权限下还有子权限');
			}		
		}
	}
}