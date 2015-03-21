<?php
/**
 +----------------------------------------------------------------------------
 * 内容模块管理 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33 <fanrong33#qq.com>
 * @version v1.1.2 Build 20140307
 +------------------------------------------------------------------------------
 */
class ModuleAction extends AdminCommonAction{
	
	/**
	 * 查询内容模块
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function index(){
		$name = $this->getActionName();
		$model = D($name);
		
		$map = array();
		$list = $this->_list($model, $map, array('orderid', 'id'), array('asc', 'desc'));
		
		$this->display();
	}
	
	/**
	 * 保存排序
	 * @author fanrong33 <fanrong33#qq.com>
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
	 * 添加内容模块
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function add(){
		
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			$_POST['name'] 	= trim($this->_post('name'));
			
			$_validate = array(
				array('title'	, 'require', '内容模块名称不能为空'),
				array('name'	, 'require', '内容模块英文名不能为空'),
				array('name'	, ''	   , '该英文名已被使用', 1, 'unique'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}

			$data['code'] 		= 1;
			$data['version'] 	= '1.0.0';
			$data['update_time']= time();
			$insert_id = $model->add($data);
			if($insert_id > 0){
				if($_POST['submit_continue']){
					$this->success('添加成功');
				}else{
					$this->success('添加成功', cookie('_currentUrl_'));
				}
			}else{
				$this->error('添加失败');
			}
		}
		
		$this->display('edit');
	}
	
	/**
	 * 修改内容模块
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function edit(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			$_POST['title'] = trim($this->_post('title'));
			$_POST['name'] 	= trim($this->_post('name'));
			
			$_validate = array(
				array('title'	, 'require', '内容模块名称不能为空'),
				array('name'	, 'require', '内容模块英文名不能为空'),
				array('name'	, ''	   , '该英文名已被使用', 1, 'unique'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			$effect = $model->save($data);
			if($effect){
				$this->success('修改成功');
			}else{
				$this->success('修改失败');
			}
		}
		
		$id = $this->_get('id', '参数id不能为空');
		
		$module = $model->find($id);
		if(!$module){
			$this->error('该内容模块不存在');
		}
		
		$this->assign('_model', $module);
		$this->display();
	}
	
	/**
	 * 删除内容模块
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function delete(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			$id = $this->_post('id', '参数id不能为空');
			
			$effect = D('Module')->delete($id);
			if($effect){
				
				// 同时删除关联的文件和表数据
				if($_POST['delete_relation'] && $_POST['structure']){
					$pieces = $_POST['structure'];
							
					foreach($pieces as $val){
						if(substr($val, 0, 4) == '[db]'){
							$val = str_replace('[db]', '', $val);
							
							// 同时删除数据表
							$sql = 'DROP TABLE IF EXISTS ' . $val;
							$model = new Model();
							$result = $model->execute($sql);
						}else{
							// 删除文件
							$result = unlink(APP_PATH.$val);
							
							// 若目录下为空，则同时删除该目录
							$dir_name = dirname(APP_PATH.$val);
							if(dir_is_empty($dir_name)){
								rmdir($dir_name);
							}
						}
					}
				}
				$this->ajaxReturn($effect, '删除成功', 1);
			}else{
				$this->ajaxReturn($effect, '删除失败', 0);
			}
		}
	}
	
	/**
	 * 显示添加内容模块对话框
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function paglet_module(){
		// 调用接口获取云商店所有应用列表
		import("@.ORG.Api.Cloudstore");
		
		$cloudstore = new cloudstorePHP();
		$json = $cloudstore->get_module_list();
		if($json['status']==1){
			$this->assign('module_list', $json['data']);
		}
		
		// 获取系统已安装内容模块列表信息
		$installed_list = D("Module")->select();
		$installed_map = array_key_list($installed_list, 'name');
		$this->assign('installed_map', $installed_map);
				
		$this->display();
	}
	
	/**
	 * 安装内容模块
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function install_module(){
		if($this->isPost()){
			$module_id = intval($_POST['module_id']);
			
			// module name必须唯一，安装过就不能再安装
			// 调用接口获取云商店对应id应用
			import("@.ORG.Api.Cloudstore");
			
			$cloudstore = new cloudstorePHP();
			$json = $cloudstore->get_module($module_id);
			$module = $json['data'];
			
			$installed_module = D("Module")->getByName($module['name']);
			if($installed_module){
				if($installed_module['code'] >= $module['code']){
					$this->ajaxReturn(null, '已添加该内容模块', 0);
				}
			}
			
			
			// 下载module zip压缩文件到临时目录
			import("@.ORG.Net.Http");
			$response = Http::fsockopen_download($module['zip_url']);
			
			$module_zip = TEMP_PATH.$module['name'].'.zip';
			$write_bytes = file_put_contents($module_zip, $response);
			if(!$write_bytes){
				unlink($module_zip);
				$this->ajaxReturn(null, '写入文件失败', 0);
			}
			if($module['file_size'] != $write_bytes){
				$this->ajaxReturn(null, '内容模块安装包下载失败，可能文件不存在', 0);
			}
			
			// 解压module到相应的目录下进行安装
			import('@.ORG.Io.Zip');
			set_time_limit(0);
			
			$zip = new Zip();
			$result=$zip->extract($module_zip, APP_PATH);
			
			// 安装完成时自动删除下载的安装包
			unlink($module_zip);
			
			if($result){
				
				if(!$installed_module){
					// 1、安装内容模块
					
					// 写入已安装内容模块数据表中
					unset($module['id']);
					$insert_id = D("Module")->add($module);
					
					$result = array();
					$result['id'] 	= $insert_id;
					$result['name'] = $module['name'];
					$result['title']= $module['title'];
					$result['sql']	= $module['sql'];
					$this->ajaxReturn($result, '安装成功', 1);
				}else{
					if($installed_module['code'] < $module['code']){
						
						// 2、升级已安装内容模块的信息
						$data = array();
						$data['description'] 	= $module['description'];
						$data['structure'] 		= $module['structure'];
						$data['code'] 			= $module['code'];
						$data['version'] 		= $module['version'];
						$data['update_time'] 	= $module['update_time'];
						D("Module")->where(array('id'=>$installed_module['id']))->save($data);
						
						$result = array();
						$result['update'] 	= true;
						$result['name'] 	= $module['name'];
						$result['title']	= $module['title'];
						$result['sql']		= $module['sql'];
						$this->ajaxReturn($result, '升级成功', 1);
					}
				}
			}else{
				$this->ajaxReturn(null, '安装失败', 0);
			}
		}
	}
	
	/**
	 * 判断是否存在表
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function exist_table(){
		$model_name = $this->_get('module_name');
		if($model_name){
			try{
				$count = D($model_name)->count();
				$table_name = D($model_name)->getTableName();
				// 存在
				$this->ajaxReturn('', "数据库中已存在表 $table_name, 共 $count 条记录", 1);
			}catch(Exception $e){
				// 不存在
				$this->ajaxReturn('', "数据库中不存在该模型对应的表, 你可能需要手动创建", 1);
			}
		}
	}
	
	/**
	 * 执行选中的sql语句
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function execute_sql(){
		// 只有超级管理员才有该权限
		if($this->_admin['role_id'] != 1){
			$this->ajaxReturn('', '只有超级管理员才有该权限', 0);
		}
		$sql_str = $_POST['sql'];
		$model = new Model();
		
		try{
			$effect = $model->execute($sql_str);
			$this->ajaxReturn('', '执行sql语句成功', 1);
		}catch(Exception $e){
			$this->ajaxReturn('', '1次只能执行1条sql语句, 执行sql语句失败, 可能该表已经存在', 0);
		}
	}
	
	/**
	 * 显示确定删除模块对话框
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function paglet_delete_module(){
		
		$module_id = $_GET['module_id'];
		$module = D('Module')->find($module_id);
		$module['structure'] = explode("\n", $module['structure']);
		
		$this->assign('_model', $module);
		$this->display();
	}
	
}
?>