<?php
/**
 +----------------------------------------------------------------------------
 * 用户管理 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.0.6 Build 20140311
 +------------------------------------------------------------------------------
 */
class MemberAction extends AdminCommonAction{
	
	/**
	 * 查询用户
	 */
	public function index(){
		$keyword = $_GET['keyword'] ? htmlspecialchars(trim($_GET['keyword'])) : '';
		
		$name = $this->getActionName();
		$model = D($name);
		
		$map = array();
		if($keyword != ''){
			if(!is_utf8($keyword)){
				$keyword = iconv("gb2312", "utf-8", $keyword);
			}
			$where['username'] 	= array('like', "%$keyword%");
			$where['email']		= array('like', "%$keyword%");
			$where['_logic']	= 'or';
			$map['_complex']	= $where;
		}
		
		$list = $this->_list($model, $map, 'id', 'desc');
		
		$this->display();
	}
	
	/**
	 * 删除用户
	 */
	public function delete(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			// 批量删除
			if($_POST['ids']){
				$this->_delete($model, $_POST['ids']);
			}
			$this->ajaxReturn('', '批量删除成功', 1);
		}else{
			// 单个删除
			$id = $this->_get('id', '参数id不能为空');
			
			$effect = $model->delete($id);
			if($effect){
				$this->success('删除成功');
			}
		}
	}
	
	/**
	 * 导入用户
	 */
	public function import(){
		
		if($this->isPost()){
			
			set_time_limit(0);
			
			if(!$_FILES['excel_file']['tmp_name']){
				$this->error('请选择需要导入的文件');
			}
			
			$excel_file = $_FILES['excel_file']['tmp_name'];
			
			import('@.ORG.Util.ExcelUtil');
			$sheet = ExcelUtil::import($excel_file);
			
			// 通过Excel批量导入数据
			$model = D('Member');
			$success_count 	= 0;
			$fail_count 	= 0;
			$repeat_count 	= 0;
			
			foreach($sheet as $key=>$row){
				$username	= trim($row[0]);
				$password	= trim($row[1]);
				$email		= trim($row[2]);
				
				$cond = array();
				$cond['username'] = $username;
				$member = $model->where($cond)->find();
				
				
				$data = array();
				$data['username']	= $username;
				$data['password'] 	= encrypt_pwd($password);
				$data['email']	 	= $email;
				$data['create_time']= time();
				$data['dateline']	= date('Y-m-d');
				
				if($member){
					if($_POST['repeat_record'] == 1){
						// 如遇到重复的记录，不导入
						$repeat_count++;
						continue;
					}else{
						// 如遇到重复的记录，覆盖
						$effect = $model->where(array('id'=>$member['id']))->save($data);
						if($effect){
							$success_count++;
						}else{
							$fail_count++;
						}
						continue;
					}
				}
				
				// 插入数据库
				$effect = $model->add($data);
				if($effect){
					$success_count++;
				}else{
					$fail_count++;
				}
			}
			// 导入成功，共导入 1 个联系人
			$this->success("导入成功，共成功导入 $success_count 个，失败 $fail_count 个，重复 $repeat_count 个");
		}
		
		$this->display();
	}
	
	
	/**
	 * 导出全部用户
	 */
	public function export(){
		
		set_time_limit(0);
		
		$header_map = array(
			'id'		  => 'ID', 
			'username'	  => '用户名', 
			'email'		  => 'Email', 
			'create_time' => '创建时间'
		);
		
		$member_list = M('Member')->field("id,username,email,FROM_UNIXTIME(create_time, '%Y-%m-%d') create_time")->select();
		
		import('@.ORG.Util.ExcelUtil');
		ExcelUtil::export($header_map, $member_list, 'member用户');
	}
	
}
?>