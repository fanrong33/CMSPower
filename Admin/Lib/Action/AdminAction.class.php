<?php
/**
 +----------------------------------------------------------------------------
 * 管理员管理 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.1.6 Build 20140401
 +------------------------------------------------------------------------------
 */
class AdminAction extends AdminCommonAction{
	
	/**
	 * 管理员管理
	 */
	public function index(){
		$keyword 	= $_GET['keyword'] ? htmlspecialchars(trim($_GET['keyword'])) : '';
		
		$model = D("AdminView");
		
		$map = array();
		if($keyword){
			$map['username'] = array('like', "%$keyword%");
			$this->assign('keyword', $keyword);
		}
		$this->_list($model, $map, 'id', 'asc');
		$this->display();
	}
	
	
	/**
	 * 添加管理员
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
		if($this->isPost()){
			$_POST['username'] = trim($this->_post('username'));
			
			$_validate = array(
				array('role_id'	, 'require', '请选择角色'),
				array('username', 'require', '用户名不能为空'),
				array('password', 'require', '新密码不能为空'),
				array('username', ''	   , '该用户名已被使用', 1, 'unique'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			$data['password'] 	= encrypt_pwd($data['password']);
			$data['create_time']= time();
			$insert_id = $model->add($data);
			if($insert_id > 0){
				// "保存并继续添加"
				if($_POST['submit_continue']){
					$this->success('添加成功');
				}else{
					$this->success('添加成功', cookie('_currentUrl_'));
				}
			}else{
				$this->error('添加失败');
			}
		}
		
		// 获取角色列表
		$role_list = D("Role")->order('orderid asc,id asc')->select();
		
		$this->assign('role_list', $role_list);
		$this->display('edit');
	}
	
	
	/**
	 * 修改管理员
	 */
	public function edit(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			if($_POST['id'] == 1){
				$this->error('不允许修改创始人');
			}
			
			$_POST['username'] = trim($this->_post('username'));
			
			$_validate = array(
				array('username', 'require', '用户名不能为空'),
				array('role_id'	, 'require', '请选择角色'),
				array('username', ''	   , '该用户名已被使用', 1, 'unique'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			// 为空表示不修改密码
			if($data['password'] == ''){
				unset($data['password']);
			}else{
				$data['password'] = encrypt_pwd($data['password']);
			}
			
			$effect = $model->save($data);
			if($effect){
				$this->success('修改成功');
			}else{
				$this->success('修改失败');
			}
		}
		
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('参数id不能为空');
		}
		$admin = $model->find($id);
		if(!$admin){
			$this->error('管理员不存在');	
		}
		
		// 获取角色列表
		$role_list = D("Role")->order('orderid asc,id asc')->select();
		
		$this->assign('role_list', $role_list);
		$this->assign('_model', $admin);
		$this->display();
	}
	
	
	/**
	 * 删除管理员
	 */
	public function delete(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			if($_POST['ids']){
				foreach($_POST['ids'] as $id){
					$id = intval($id);
					if($id <= 1) continue;
					
					$model->where(array('id'=>$id))->delete(); // _after_delete()回调函数才可以调到
				}
			}
			$this->ajaxReturn('', '批量删除成功', 1);
		}else{
			$id = intval($_GET['id']);
			if(!$id){
				$this->error('参数id不能为空');
			}
			
			
			// 不允许删除id为1的创始人
			if($id > 1){
				$effect = $model->delete($id);
				if($effect){
					$this->success('删除成功');
				}
			}
		}
	}
	
}

?>