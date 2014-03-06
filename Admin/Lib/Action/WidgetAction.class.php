<?php
/**
 +----------------------------------------------------------------------------
 * 挂件管理 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.2.4 Build 20130914
 +------------------------------------------------------------------------------
 */
class WidgetAction extends AdminCommonAction{
	
	/**
	 * 挂件管理
	 */
	public function index(){
		$name = $this->getActionName();
		$model = D($name);
		
		$map = array();
		$this->_list($model, $map, array('orderid', 'id'), array('asc', 'desc'));
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
	 * 添加挂件
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			$_POST['name'] 	= trim($this->_post('name'));
			
			$_validate = array(
				array('title'	, 'require', '挂件名称不能为空'),
				array('name'	, 'require', '挂件英文名不能为空'),
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
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		
		$this->display('edit');
	}
	
	
	/**
	 * 修改挂件
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
		
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('id参数为空');
		}
		
		$widget = $model->find($id);
		if(!$widget){
			$this->error('该挂件不存在');
		}
		
		$this->assign('_model', $widget);
		$this->display();
	}
	
	
	/**
	 * 删除挂件
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
			$id = intval($_GET['id']);
			if(!$id){
				$this->error('参数id不能为空');
			}
			
			$effect = $model->delete($id);
			if($effect){
				$this->success('删除成功');
			}
		}
	}
	
	
	/**
	 * 显示添加挂件对话框
	 */
	public function paglet_widget(){
		// 调用接口获取云商店所有挂件列表
		import("@.ORG.Api.Cloudstore");
		
		$cloudstore = new cloudstorePHP();
		$json = $cloudstore->get_widget_list();
		
		if($json['status']==1){
			$this->assign('widget_list', $json['data']);
		}
		
		// 获取系统已安装挂件列表
		$installed_list = D("Widget")->select();
		$installed_map = array_key_list($installed_list, 'name');
		$this->assign('installed_map', $installed_map);
		
		$this->display();
	}
	
	
	/**
	 * 安装挂件到前台
	 */
	public function install_widget(){
		if($this->isPost()){
			$widget_id = intval($_POST['widget_id']);
			
			// widget name必须唯一，安装过就不能再安装
			// 调用接口获取云商店对应id挂件
			import("@.ORG.Api.Cloudstore");
			
			$cloudstore = new cloudstorePHP();
			$json = $cloudstore->get_widget($widget_id);
			$widget = $json['data'];
			
			$installed_widget = D("Widget")->getByName($widget['name']);
			if($installed_widget){
				if($installed_widget['code'] >= $widget['code'])
				$this->ajaxReturn(null, '已添加该挂件', 0);
			}else{
				// 如果初次未安装该挂件，则先判断是否存在依赖组件
				if($widget['depend']){ 
					list($depend_type, $depend_text, $depend_module) = explode('|', $widget['depend']);		// app|友情链接|Link
					
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
			
			// 下载widget zip压缩文件到临时目录
			import("@.ORG.Net.Http");
			$response = Http::fsockopen_download($widget['zip_url']);
			
			$widget_zip = TEMP_PATH.$widget['name'].'.zip';
			$write_bytes = file_put_contents($widget_zip, $response);
			if(!$write_bytes){
				unlink($widget_zip);
				$this->ajaxReturn(null, '写入文件失败', 0);
			}
			if($widget['file_size'] != $write_bytes){
				$this->ajaxReturn(null, '挂件安装包下载失败，可能文件不存在', 0);
			}
			
			// 解压widget到相应的目录下进行安装
			import('@.ORG.Io.Zip');
			set_time_limit(0);
			
			$zip = new Zip();
			$result=$zip->extract($widget_zip, 'Home/');
			
			unlink($widget_zip);
			if($result){
				
				if(!$installed_widget){
					// 1、安装应用
					
					// 写入已安装挂件数据表中
					unset($widget['id']);
					$insert_id = D("Widget")->add($widget); 
					
					$this->ajaxReturn(null, '安装成功', 1);
					
				}else{
					if($installed_widget['code'] < $widget['code']){
						// 2、升级已安装应用的信息
						$data = array();
						$data['code'] 		 = $widget['code'];
						$data['version'] 	 = $widget['version'];
						$data['update_time'] = $widget['update_time'];
						D("Widget")->where(array('id'=>$installed_widget['id']))->save($data);
						
						$this->ajaxReturn(null, '升级成功', 1);
					}
				}
			}else{
				$this->ajaxReturn(null, '安装失败', 0);
			}
		}
	}
	
	
	/**
	 * 显示子挂件管理对话框
	 */
	public function paglet_children_widget_manager(){
		$widget_name  = $_GET['name'];
        $widget = D("Widget")->where(array('name'=>$widget_name))->find();
        
		// 获取MixWall挂件下所有子挂件的配置信息
        $template_dir = __ROOT__.'Home/Lib/Widget/'.$widget_name.'/';
        
        // 获取挂件的配置文件
        if(is_file($template_dir.'config.php')){
        	$config_list = include $template_dir.'config.php';
        	
			$this->assign('list', $config_list);
        }
        
        $this->assign('widget_title', $widget['title']);
        $this->assign('widget_name', $widget_name);
		$this->display();
	}
	
	
	/**
	 * 显示子挂件配置对话框
	 */
	public function paglet_children_widget_config(){
		if($this->isPost()){
			$widget_name = $_POST['widget_name'];
			$name 		 = $_POST['name'];
		}else{
	        $widget_name = $_GET['widget_name']; 	// 挂件英文名
	        $name 		 = $_GET['name']; 			// 子挂件英文名，用于获取子挂件配置信息
		}
		
		// 获取所有子挂件配置信息
        $template_dir = __ROOT__.'Home/Lib/Widget/'.$widget_name.'/';
		$config_map = include $template_dir.'config.php';
		
		if($this->isPost()){
			$config_map[$name] = $_POST;
			
			// 将配置写入挂件目录下
        	GF('config', $config_map, $template_dir);
        	
        	$this->ajaxReturn(null, 'success', 1);
		}
		
        $this->assign('_config', $config_map[$name]);
		$this->display($template_dir.'config.html');
	}
	
	/**
	 * 添加子挂件
	 */
	public function paglet_add_children_widget_config(){
		if($this->isPost()){
			$widget_name = $_POST['widget_name'];
			unset($_POST['widget_name']);
			$_POST['name'] = trim($_POST['name']);
			
			// 获取所有子挂件配置信息
	        $template_dir = __ROOT__.'Home/Lib/Widget/'.$widget_name.'/';
			$config_map = include $template_dir.'config.php';
			$config_map[$_POST['name']] = $_POST;
			
			// 将配置写入挂件目录下
        	GF('config', $config_map, $template_dir);
        	
        	$this->ajaxReturn(null, 'success', 1);
		}
		$widget_name = $_GET['widget_name'];
		
		$template_dir = __ROOT__.'Home/Lib/Widget/'.$widget_name.'/';
		
		$this->assign('widget_name', $widget_name);
		$this->display($template_dir.'config.html');
	}
	
	/**
	 * 删除子挂件配置
	 */
	public function delete_children_widget_config(){
		if($this->isPost()){
			$widget_name = $_POST['widget_name']; 	// 挂件英文名
		    $name 		 = $_POST['name']; 			// 子挂件英文名，用于获取子挂件配置信息
		    
		    // 获取所有子挂件配置信息
	        $template_dir = __ROOT__.'Home/Lib/Widget/'.$widget_name.'/';
			$config_map = include $template_dir.'config.php';
			
			unset($config_map[$name]);
			
			// 将配置写入挂件目录下
        	GF('config', $config_map, $template_dir);
        	
        	$this->ajaxReturn(null, '删除成功', 1);
		}
	}
	
}
?>