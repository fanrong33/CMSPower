<?php
/**
 +----------------------------------------------------------------------------
 * 角色管理 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.2.4 Build 20140224
 +------------------------------------------------------------------------------
 */
class RoleAction extends AdminCommonAction{
	
	/**
	 * 角色管理
	 */
	public function index(){
		$name = $this->getActionName();
		$model = D($name);
		
		$map = array();
		$this->_list($model, $map, array('orderid', 'id'), array('asc', 'asc'));
		
		$this->display();
	}
	
	
	/**
	 * 保存排序
	 */
	public function sort(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			if($_POST['orderid']){
				// 保存排序
				$this->_sort($model, $_POST['orderid']);
			}
			$this->ajaxReturn('', '保存排序成功', 1);
		}
	}
	
	
	/**
	 * 添加角色
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
		if($this->isPost()){
			
			$_POST['name'] 			= trim($this->_post('name'));
			$_POST['description'] 	= trim($this->_post('description'));
			
			$_validate = array(
				array('name', 'require', '角色名不能为空'),
				array('name', ''	   , '该角色名已被使用', 1, 'unique'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}

			$data['resources'] 	= serialize($data['resources']);
			$data['channels'] 	= serialize($data['channels']);
			$data['type'] 		= '3'; // 只允许添加后台普通管理员角色
			$data['create_time']= time();
			$insert_id = $model->add($data);
			if($insert_id > 0){
				// “保证并继续添加”
				if($_POST['submit_continue']){
					$this->success('添加成功');
				}else{
					$this->success('添加成功', cookie('_currentUrl_'));
				}
			}else{
				$this->error('添加失败');
			}
		}
		
		// 获取启用的nav tree
		$cond = array();
		$cond['disabled'] = '0';
    	$nav_list = D("Nav")->where($cond)->order('orderid asc')->select();
		$nav_tree = list_to_tree($nav_list);
		
		// 获取nav应用对应的资源权限
		$resource_list = D("Resource")->order('orderid asc')->select();
		$tmp = array();
		foreach($resource_list as $rs){
			$rs['methods'] = explode('|', $rs['methods']);
			$tmp[] = $rs;
		}
		$resource_list = $tmp;
		unset($tmp);
		
		$resource_map = array_key_list($resource_list, 'name');
		
		
		// 获取栏目树状列表, 关联获取得到栏目所属模型
		$channel_table  = D("Channel")->getTableName();
		$module_table	= D("Module")->getTableName();
		$sql = <<<EOF
select a.*,b.name as module_name,b.title as module_title 
from $channel_table a left join $module_table b 
on a.module_id=b.id 
order by orderid asc 
EOF;
		
		$list = $model->query($sql);
		$channel_tree = list_to_tree($list);
		
		$this->assign('_nav', $nav_tree);
		$this->assign('_resource_map', $resource_map);
		$this->assign('_channel_tree', $channel_tree);
		$this->display('edit');
	}
	
	
	/**
	 * 修改角色
	 */
	public function edit(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			$_POST['username'] 		= trim($this->_post('username'));
			$_POST['description'] 	= trim($this->_post('description'));
			
			if(false === $data = $model->create()){
				$this->error($model->getError());
			}
			
			$data['resources']	= serialize($data['resources']);
			$data['channels']	= serialize($data['channels']);
			$effect = $model->save($data);
			if($effect){
				$this->success('修改成功');
			}else{
				$this->success('修改失败');
			}
		}
		
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('id参数为空');
		}
		$role = $model->find($id);
		if(!$role){
			$this->error('角色不存在');	
		}
		
		$role['resources'] = unserialize($role['resources']);
		$role['channels'] = unserialize($role['channels']);

		
		// 获取nav tree
		$cond = array();
		$cond['disabled'] = '0';
    	$nav_list = D("Nav")->where($cond)->order('orderid asc')->select();
		$nav_tree = list_to_tree($nav_list);
		
		// 获取应用的权限资源
		$resource_list = D("Resource")->order('orderid asc')->select();
		$tmp = array();
		foreach($resource_list as $rs){
			$rs['methods'] = explode('|', $rs['methods']);
			$tmp[] = $rs;
		}
		$resource_list = $tmp;
		unset($tmp);
		
		$resource_map = array_key_list($resource_list, 'name');
		
		
		// 获取栏目树状列表,关联获取得到栏目所属模型
		$channel_table  = D("Channel")->getTableName();
		$module_table	= D("Module")->getTableName();
		$sql = <<<EOF
select a.*,b.name as module_name,b.title as module_title 
from $channel_table a left join $module_table b 
on a.module_id=b.id 
order by orderid asc 
EOF;
		
		$channel_list = $model->query($sql);
		$channel_tree = list_to_tree($channel_list);
		
		$this->assign('_model', $role);
		$this->assign('_nav', $nav_tree);
		$this->assign('_resource_map', $resource_map);
		$this->assign('_channel_tree', $channel_tree);
		$this->display();
	}
	
	
	/**
	 * 删除角色
	 */
	public function delete(){
		$name = $this->getActionName();
		$model = D($name);
		
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('id参数为空');
		}
		$role = $model->find($id);
		if($role['type'] != '3'){
			$this->error('系统内置角色,不允许删除');
		}
		
		// 判断该角色是否被使用
		if(D('Admin')->where(array('role_id'=>$role['id']))->find()){
			$this->error('该存在管理员使用该角色,不允许删除');
		}
		
		
		$effect = $model->delete($id);
		if($effect){
			$this->success('删除成功');
		}
	}
	
}

?>