<?php
/**
 +----------------------------------------------------------------------------
 * 修改个人密码 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.0.1 Build 20140210
 +------------------------------------------------------------------------------
 */
class EditPasswordAction extends AdminCommonAction{
	
	/**
	 * 修改个人密码
	 */
	public function index(){
		if($this->isPost()){
			$model = D("Admin");
			
			$_validate = array(
				array('password'	, 'require'		, '原密码不能为空'),
				array('newpassword'	, 'require'		, '新密码不能为空'),
				array('repassword'	, 'require'		, '确认密码不能为空'),
				array('repassword'	, 'newpassword'	, '确认密码与新密码不相同', 1, 'confirm'), // 验证确认密码是否和密码一致
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			// 验证原密码
			$admin = $model->find($this->_admin_id);
			if(encrypt_pwd($data['password']) != $admin['password']){
				$this->error('原密码不正确');
			}
			
			// 修改为新密码
			$effect = $model->where(array('id'=>$this->_admin_id))->save(array('password'=>encrypt_pwd($_POST['newpassword'])));
			if($effect){
				$this->success('密码修改成功');
			}
		}
		
		$this->display();
	}
	
}
?>