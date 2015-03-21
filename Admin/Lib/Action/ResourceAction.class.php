<?php
/**
 +----------------------------------------------------------------------------
 * 资源管理 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.0.4 Build 20140224
 +------------------------------------------------------------------------------
 */
class ResourceAction extends AdminCommonAction{
	
	/**
	 * 查询资源
	 */
	public function index(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			if(isset($_POST['orderid'])){
				// 保存排序
				$this->_sort($model, $_POST['orderid']);
			}
		}
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
	 * 添加资源
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
		if($this->isPost()){
			
			$_POST['title']	= trim($this->_post('title'));
			$_POST['name']	= trim($this->_post('name'));
			
			$_validate = array(
				array('name'	, 'require'	, '控制器类名不能为空'),
				array('name'	, ''		, '该控制器类名已被使用', 1, 'unique'),
				array('title'	, 'require'	, '资源名称不能为空'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			$data['methods'] 	= join('|', $data['methods']);
			$data['orderid'] 	= 0;
			$data['create_time']= time();
			
			$insert_id = $model->add($data);
			if($insert_id > 0){
				// “保存并继续添加”
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
	 * 修改资源
	 */
	public function edit(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			$_POST['name']	= trim($this->_post('name'));
			
			$_validate = array(
				array('name'	, 'require'	, '控制器类名不能为空'),
				array('name'	, ''		, '该控制器类名已被使用', 1, 'unique'),
				array('title'	, 'require'	, '资源名称不能为空'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			// 过滤空的methods
			$data['methods'] = array_filter($data['methods']);
			$data['methods'] = join('|', $data['methods']);
			
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
		
		$resource = $model->find($id);
		if(!$resource){
			$this->error('资源不存在');
		}
		$resource['methods'] = explode('|', $resource['methods']);
		
		$this->assign('_model', $resource);
		$this->display();
	}
	
	
	/**
	 * 删除资源
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
	 * 自动获取类方法
	 */
	public function get_class_public_methods(){
		// 待过滤方法
		$black_list = explode(',', '_initialize,getReturnUrl,get,__set,__get,__call,__construct,__destruct');
		
		if($this->isPost()){
			
			$class_name = trim($this->_post('class_name'));
			if($class_name == ''){
				$this->ajaxReturn('', '控制器类名不能为空', 0);
			}
			
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
			$this->ajaxReturn($result, '获取类的所有公共方法成功', 1);
		}
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