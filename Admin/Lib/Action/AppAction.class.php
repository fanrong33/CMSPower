<?php
/**
 +----------------------------------------------------------------------------
 * 应用管理 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.2.4 Build 20131206
 +------------------------------------------------------------------------------
 */
class AppAction extends AdminCommonAction{
	
	/**
	 * 查询应用
	 */
	public function index(){
    	$nav_list = D("Nav")->order('orderid asc')->select();
    	
    	$nav_tree = list_to_tree($nav_list);
    	$this->assign('_nav', $nav_tree);
		
		$this->display();
	}
	
	
	/**
	 * 对nav进行排序
	 */
	public function sort(){
		if($this->isPost()){
			if(is_array($_POST['order'])){
				$prefix = 'navbar_';
				foreach($_POST['order'] as $key=>$val){
					if(false !== strpos($val, 'nav_')){
						$prefix = 'nav_';
					}
					
					$id = intval(substr($val,strlen($prefix)));
					$cond = array();
					$cond['id'] = $id;
					$cond['pid']= $_POST['pid'];
					$effect = D("Nav")->where($cond)->save(array('orderid'=>$key+1));
				}
				$this->ajaxReturn(null, 'success', 1);
			}
		}
	}
	
	/**
	 * 将nav移动到另一个tab下
	 * 修改nav ID的父ID,并修改orderid为9999（最后一个）
	 */
	public function recive(){
		if($this->isPost()){
			$data = array();
			$data['pid'] 	= $_POST['pid'];
			$data['orderid']= 999999;
			$effect = D("Nav")->where(array('id'=>$_POST['id']))->save($data);
			
			$this->ajaxReturn(null, 'success', 1);
		}
	}
	
	/**
	 * 添加导航（通用）
	 */
	public function add_nav(){
		if($this->isPost()){
			
			if(false === $data = D("Nav")->create()){
				$this->error(D("Nav")->getError());
			}
			
			switch($data['type']){
				case 'navbar':
					$data['pid'] = 0;
					break;
				default:
					break;
			}
			$data['disabled']		= '0';
			$data['orderid']		= 999999;
			$data['create_time'] 	= time();
			
			$insert_id = D("Nav")->add($data);
			if($insert_id){
				$nav = array();
				$nav['id'] 		= $insert_id;
				$nav['pid']		= $data['pid'];
				$nav['name'] 	= $data['name'];
				$nav['title'] 	= $data['title'];
				$nav['icon'] 	= $data['icon'];
				$this->ajaxReturn($nav, '添加成功', 1);
			}else{
				$this->ajaxReturn(null, '添加失败', 0);
			}
		}
	}
	
	
	/**
	 * 删除导航（通用）
	 */
	public function delete_nav(){
		if($this->isPost()){
			// 只删除 nav 信息
			$id = intval($_POST['id']);
			if(!$id){
				$this->error('id参数为空');
			}
			
			$nav = D("Nav")->find($id);
			if($nav['type'] == 'header' && $nav['title'] == '内容 CONTENT'){
				$this->ajaxReturn(null, '内容 CONTENT 不允许删除', 0);
			}
			
			
			$effect = D("Nav")->delete($id);
			if($effect){
				if($nav['type'] == 'nav'){
					// 若为应用，同时删除 app已安装应用中的信息
					$cond = array();
					$cond['name'] = $nav['name'];
					D("App")->where($cond)->delete();
					
					// 若存在资源表，则同时删除关联的资源权限
					D('Resource')->where($cond)->delete();
					
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
				}
				$this->ajaxReturn($effect, '删除成功', 1);
			}else{
				$this->ajaxReturn($effect, '删除失败', 0);
			}
		}
	}
	
	
	
	
	/**
	 * 禁用导航
	 */
	public function disable_nav(){
		if($this->isPost()){
			// 只删除 nav 信息
			$id = intval($_POST['id']);
			if(!$id){
				$this->error('id参数为空');
			}
			
			$effect = D("Nav")->where(array('id'=>$id))->save(array('disabled'=>$_POST['disabled']));
			if($effect){
				$this->ajaxReturn($effect, '禁用成功', 1);
			}else{
				$this->ajaxReturn($effect, '禁用失败', 0);
			}
		}
	}
	
	/**
	 * 显示添加应用对话框
	 */
	public function paglet_app(){
		// 调用接口获取云商店所有应用列表
		import("@.ORG.Api.Cloudstore");
		
		$cloudstore = new cloudstorePHP();
		$json = $cloudstore->get_app_list();
		if($json['status']==1){
			$this->assign('app_list', $json['data']);
		}
		
		// 获取系统已完整应用列表信息
		$installed_list = D("App")->select();
		$installed_map = array_key_list($installed_list, 'name');
		$this->assign('installed_map', $installed_map);
				
		$this->display();
	}
	
	/**
	 * 安装应用，并且添加到nav
	 * 	如果已安装，且有更新的code代码，则进行升级
	 */
	public function install_app(){
		if($this->isPost()){
			$app_id = intval($_POST['app_id']);
			
			// app name必须唯一，安装过就不能再安装
			// 调用接口获取云商店对应id应用
			import("@.ORG.Api.Cloudstore");
			
			$cloudstore = new cloudstorePHP();
			$json = $cloudstore->get_app($app_id);
			$app = $json['data'];
			
			$installed_app = D("App")->getByName($app['name']);
			if($installed_app){
				if($installed_app['code'] >= $app['code']){
					$this->ajaxReturn(null, '已添加该应用', 0);
				}
			}else{
				// 如果初次未安装该挂件，则先判断是否存在依赖组件
				if($app['depend']){ 
					list($depend_type, $depend_text, $depend_module) = explode('|', $app['depend']);		// app|用户管理|Member
					
					if($depend_type == 'app'){
						$depend_type = '后台应用';
					}
					try{
						$DependModule = D($depend_module);
						$pk = $DependModule->getPk();
					}catch(Exception $e){
						$this->ajaxReturn(null, '检测到系统中不存在依赖表'.$depend_module.'，请先安装“'.$depend_text.'”'.$depend_type, 0);
					}
				}
			}
			
			// 下载app zip压缩文件到临时目录
			import("@.ORG.Net.Http");
			$response = Http::fsockopen_download($app['zip_url']);
			
			$app_zip = TEMP_PATH.$app['name'].'.zip';
			$write_bytes = file_put_contents($app_zip, $response);
			if(!$write_bytes){
				unlink($app_zip);
				$this->ajaxReturn(null, '写入文件失败', 0);
			}
			if($app['file_size'] != $write_bytes){
				$this->ajaxReturn(null, '应用安装包下载失败，可能文件不存在', 0);
			}
			
			// 解压app到相应的目录下进行安装
			import('@.ORG.Io.Zip');
			set_time_limit(0);
			
			$zip = new Zip();
			$result=$zip->extract($app_zip, APP_PATH);
			
			// 安装完成时自动删除下载的安装包
			unlink($app_zip);
			
			if($result){
				
				if(!$installed_app){
					// 1、安装应用
					
					// 写入已安装应用数据表中
					unset($app['id']);
					$insert_id = D("App")->add($app); 
					
					// 添加到nav
					$data = array();
					$data['pid']			= $_POST['pid'];
					$data['type']			= 'nav';
					$data['name']			= $app['name'];
					$data['title']			= $app['title'];
					$data['disabled']		= '0';
					$data['orderid']		= 999999;
					$data['create_time'] 	= time();
					$insert_id = D("Nav")->add($data);
					
					$this->add_resource($app['name']);
					
					$nav = array();
					$nav['id'] 		= $insert_id;
					$nav['name'] 	= $app['name'];
					$nav['title'] 	= $app['title'];
					$nav['sql']		= $app['sql'];
					$this->ajaxReturn($nav, '安装成功', 1);
				}else{
					if($installed_app['code'] < $app['code']){
						
						// 2、升级已安装应用的信息
						$data = array();
						$data['description'] 	= $app['description'];
						$data['structure'] 		= $app['structure'];
						$data['code'] 			= $app['code'];
						$data['version'] 		= $app['version'];
						$data['update_time'] 	= $app['update_time'];
						D("App")->where(array('id'=>$installed_app['id']))->save($data);
						
						$this->add_resource($app['name']);
						
						$nav = array();
						$nav['update'] 	= true;
						$nav['name'] 	= $app['name'];
						$nav['title'] 	= $app['title'];
						$nav['sql']		= $app['sql'];
						$this->ajaxReturn($nav, '升级成功', 1);
					}
				}
			}else{
				$this->ajaxReturn(null, '安装失败', 0);
			}
		}
	}
	
	/**
	 * 添加应用url资源到资源表
	 */
	private function add_resource($app_name){
		// 判断资源表中是否存在对应资源，若不存在，则将url资源插入资源表中
		$resource = D('Resource')->where(array('name'=>$app_name))->find();
		if(!$resource){
			$class_data = $this->get_class_public_methods($app_name);
			
			$method_list = array();
			foreach($class_data['method_list'] as $method){
				$method_list[] = $method['name'].':'.$method['comment'];
			}
			
			$data = array();
			$data['name']		 = $app_name;
			$data['title']		 = $class_data['class_comment'];
			$data['methods'] 	 = join('|', $method_list);
			$data['create_time'] = time();
			$insert_id = D('Resource')->add($data);
		}
	}
	
	/**
	 * 判断是否存在表
	 */
	public function exist_table(){
		$model_name = $this->_get('app_name');
		if($model_name == 'Sw'){
			$model_name = 'Ad';
		}
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
	 * 显示确定删除应用对话框
	 */
	public function paglet_nav(){
		
		$nav_id = $_GET['id'];
		$nav = D("Nav")->find($nav_id);
		$app = D('App')->where(array('name'=>$nav['name']))->find();
		$app['structure'] = explode("\n", $app['structure']);
		
		$this->assign('_model', $app);
		$this->display();
	}
	
	/**
	 * 自动获取类方法
	 */
	private function get_class_public_methods($class_name){
		// 待过滤方法
		$black_list = explode(',', '_initialize,getReturnUrl,get,__set,__get,__call,__construct,__destruct');
		
		try {
			$method_list = $this->get_class_all_methods($class_name.'Action', 'public');
			$class_comment = $this->get_class_comment($class_name.'Action');
		}catch( Exception $e ) {
			$this->ajaxReturn('', '请确认类 '.$class_name.'Action 是否存在', 0);
		}
		
		if($method_list){
			// 过滤魔法函数等方法
			$tmp_list = array();
			foreach($method_list as $rs){
				if(in_array($rs['name'], $black_list)){
					continue;
				}
				$tmp_list[] = $rs;
			}
			$method_list = $tmp_list;
			unset($tmp_list);
			
		}
		
		$result = array(
			'class_comment' => $class_comment,
			'method_list' => $method_list,
		);
		return $result;
		exit;
		$this->ajaxReturn($result, '获取类的所有公共方法成功', 1);
	}
	
	/**
	 * 通过反射机制获取PHP类的注释
	 */
	private function get_class_comment($class){
		$r = new ReflectionClass($class);
		
	    $docblock = $r->getDocComment();
		preg_match('/([\x{4e00}-\x{9fa5}]+)/u', $docblock, $matches);
		
		return $matches[1];
	}
	
	/**
	 * 通过反射机制获取PHP类的所有方法
	 * $methods = get_class_all_methods(get_class($this));
	 * 
	 * @param string $class	类名
	 */
	private function get_class_all_methods($class, $type=''){
	    $r = new ReflectionClass($class);
	    foreach($r->getMethods() as $key=>$methodObj){
	    	if($type && $type == 'private'){
		        if(!$methodObj->isPrivate()) continue;
	    	}
	    	if($type && $type == 'protected'){
		        if(!$methodObj->isProtected()) continue;
	    	}
	    	if($type && $type == 'public'){
		        if($methodObj->isPrivate()) continue;
		        if($methodObj->isProtected()) continue;
	    	}
	    	
	    	if($methodObj->isPrivate())
	        	$methods[$key]['type'] = 'private';
	        elseif($methodObj->isProtected())
	            $methods[$key]['type'] = 'protected';
	        else
	            $methods[$key]['type'] = 'public';
	        $methods[$key]['name'] = $methodObj->name;
	        $methods[$key]['class'] = $methodObj->class;
	        
	        // 获取方法注释
	        $docblock = $methodObj->getDocComment();
	        
        	preg_match('/([\x{4e00}-\x{9fa5}]+)/u', $docblock, $matches);
        	$methods[$key]['comment'] = $matches[1];
	    }
	    return $methods;
	}
	
}
?>