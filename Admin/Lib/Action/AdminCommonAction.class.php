<?php
/**
 +----------------------------------------------------------------------------
 * 后台应用通用 控制器类
 * 后台应用 需继承自 AdminCommonAction
 +----------------------------------------------------------------------------
 * @category 后台应用父类
 * @author fanrong33
 * @version v1.3.0 Build 20140306
 +------------------------------------------------------------------------------
 */
class AdminCommonAction extends Action{
	
	// 管理员ID
	protected $_admin_id = 0;
	protected $_admin;
	
	public function _initialize(){
		
		// 重新设置m和a参数
		if(C('URL_MODEL') == 0){
	        $module_name = (C('URL_CASE_INSENSITIVE')==true) ? parse_name(MODULE_NAME, 0) : MODULE_NAME;
			$_GET = array_merge(
				array(
					C('VAR_MODULE')=>$module_name,
					C('VAR_ACTION')=>ACTION_NAME,
				),
				$_GET
			);
		}
		
		if($_SESSION['admin_id']) $this->_admin_id = $_SESSION['admin_id'];
		
		if(!$this->_admin_id){
			U('User/login', array(), true, true);
		}
		
		$admin	= cache_get('Admin', $this->_admin_id, 60*10);
		$role 	= cache_get('Role', $admin['role_id'], 60*10);
		$admin['role_name'] = $role['name'];
		$this->_admin = $admin;
		
		
		import('@.ORG.Util.RBAC');
		if(!RBAC::accessRight($admin['role_id'])){
			$this->error('没有权限，拒绝访问');
		}
		
		// 获取当前控制器的导航栏
		$nav_tree 	  = RBAC::getRoleNavTree($admin['role_id']);
		$channel_tree = RBAC::getRoleChannelTree($admin['role_id']);
		
		// 循环为navbar设置url(第一个nav)
		$tmp_nav_tree = array();
		foreach($nav_tree as $navbar){
			if(is_array($navbar['_child']) && $navbar['_child']){
				foreach($navbar['_child'] as $nav){
					if($nav['type'] == 'nav'){
						$navbar['url'] = U($nav['name'].'/index');
						break;
					}
				}
			}
			$tmp_nav_tree[] = $navbar;
		}
		$nav_tree = $tmp_nav_tree;
		unset($tmp_nav_tree);

		
		// 根据当前的navbar得到当前的nav_child
		$nav_child = array();
		foreach($nav_tree as $key => $navbar){
			// 若在内容模块控制器里面
			$class_parent = array_shift(class_parents($this));
			if($class_parent == 'ContentParentAction'){
				if($navbar['type'] == 'navbar' && $navbar['title'] == '内容管理'){
					$nav_child = $navbar['_child'];
					$nav_tree[$key]['active'] = true; // 临时变通，为内容模块设置navbar的active^ ^，貌似有点问题，不够优雅，后面改进
					break;
				}
			}else{
				if($navbar['type'] == 'navbar' && $navbar['active'] == true){
					$nav_child = $navbar['_child'];
					break;
				}
			}
		}
		
		$this->assign('_nav_tree'		, $nav_tree); 		// 导航栏
		$this->assign('_nav_child'		, $nav_child);
		$this->assign('_channel_tree'	, $channel_tree);	// 内容栏目树
		$this->assign('_admin'			, $admin);
	}
	
	/**
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
	 */
	public function getReturnUrl(){
		return __URL__.'?'.C('VAR_MODULE').'='.MODULE_NAME.'&'.C('VAR_ACTION').'='.C('DEFAULT_ACTION');
	}
	
	public function index(){
		// 列表过滤器，生成查询Map对象
		$map = $this->_search();
		if(method_exists($this, '_filter')){
			$this->_filter($map);
		}
		$name = $this->getActionName();
		$model = D($name);
		if(!empty($model)){
			$this->_list($model, $map);
		}
		$this->display();
		return;
	}
	
	/**
	 * 根据表单生成查询条件
	 * @param string $name 数据对象名称User
	 * @return HashMap
	 */
	protected function _search($name=''){
		// 生成查询条件
		if (empty($name)){
			$name = $this->getActionName();
		}
		$model = D($name);
		$map = array();
		$fields = $model->getDbFields();
		foreach($fields as $key => $val){
			if(isset($_REQUEST[$val]) && $_REQUEST[$val]!= ''){
				$map[$val] = $_REQUEST[$val];
			}
		}
		return $map;
	}
	
	/**
	 * 根据表单生成的查询条件进行过滤
	 * 
	 * @param mixed $model 模型对象
	 * @param array $map 查询条件
	 * @param string|array $sortBy 排序字段
	 * @param string|array $orderBy 顺序(升序/倒序)
	 * 
	 * @author fanrong33
	 * @version v1.0.0 Build 20120209
	 */
	protected function _list($model, $map, $order_by='', $direction='desc'){
		// 命令 xxx 排序 xxx
	 	// &order_by=id&direction=desc
	 	
		// 排序字段 默认为主键名
	 	if(isset($_REQUEST['order_by'])){
	 		$order = $_REQUEST['order_by'];
	 		
	 		$fields = $model->getDbFields();
	 		
	 		if(!in_array($order, $fields)){
	 			$order = $model->getPk();
	 		}
	 		
	 	}else{
	 		if(is_array($order_by)){
	 			$order = $order_by;
	 		}else{
		 		$order = !empty($order_by) ? $order_by : $model->getPk();
	 		}
	 	}
	 	
	 	// 排序方向默认倒序 desc
	 	if(isset($_REQUEST['direction'])){
	 		$sort = $_REQUEST['direction'];
	 		
	 		$sort = in_array($sort, array('desc', 'asc')) ? $sort : 'desc';
	 	}else{
	 		if(is_array($direction)){
	 			$sort = $direction;
	 		}else{
	 			// order为数组，设置sort默认的数组方式
	 			if(is_array($order_by) && $count_sort=count($order_by)){
	 				for($i=0; $i<$count_sort; $i++){
	 					$sort[] = 'desc'; // 默认倒序
	 				}
	 			}else{
			 		$sort = in_array($direction, array('desc', 'asc')) ? $direction : 'desc';
	 			}
	 		}
	 	}
	 	
	 	// 取得满足条件的记录数
	 	$count = $model->where($map)->count();
	 	
	 	$list = array();
	 	if($count > 0){
	 		import('@.ORG.util.Page');
	 		// 创建分页对象
	 		if(!empty($_REQUEST['listRows'])){
	 			$listRows = $_REQUEST['listRows'];
	 		}else{
	 			$listRows = '';
	 		}
	 		$p = new Page($count, $listRows);
	 		$p->setConfig('theme', ' 共%totalRow%%header% &nbsp;%nowPage%/%totalPage% 页 &nbsp;%upPage%&nbsp;&nbsp;%downPage% &nbsp;%goto%');
	 		$p->setConfig('prev', '上页');
	 		$p->setConfig('next', '下页');
	 		
	 		// 共19条信息 上页 下页 共1页 到第  页 确定 每页20tiao
	 		
	 		// 分页查询
	 		if(isset($_REQUEST['order_by']) && isset($_REQUEST['direction'])){
			 	$list = $model->where($map)->order("`".$order.'` '.$sort)->limit($p->firstRow, $p->listRows)->select();
	 		}else{
		 		if(is_array($order_by)){
		 			$order_sort = array_combine($order, $sort);
			 		$list = $model->where($map)->order($order_sort)->limit($p->firstRow, $p->listRows)->select();
		 		}else{
			 		$list = $model->where($map)->order("`".$order.'` '.$sort)->limit($p->firstRow, $p->listRows)->select();
		 		}
	 		}
	 			
	 		
	 		if($_SERVER['QUERY_STRING'] != ''){
	 			$p->parameter .= '&';
	 		}
	 		
	 		// 跳转分页的是保证查询条件
	 		foreach($map as $key => $val){
	 			// 对$map进行过滤，只保留$_GET中的参数
	 			if(!is_array($val) && isset($_GET[$key])){
	 				$p->parameter .= "$key=" . urlencode ( $val ) . "&";
	 			}
	 		}
	 		
	 		
	 		// 分页显示
	 		$page = $p->show();
	 		
	 		if(isset($_REQUEST['order_by']) && isset($_REQUEST['direction'])){
	 			$sort = ($sort == 'asc') ? 'desc' : 'asc';
				$sort_alt = ($sort == 'asc') ? '↓' : '↑'; //排序方式
				
				$this->assign('sort', $sort);
				$this->assign('sort_alt', $sort_alt);
	 		}
			
			
	 		// 模板赋值显示
	 		$this->assign('list', $list);
	 		$this->assign('page', $page);
	 		
	 	}
	 	cookie('_currentUrl_', __SELF__);
	 	return $list;
	}
	
	/**
	 * 对表单orderid[]进行排序
	 * 
	 * @param mixed 	$model 			模型对象
	 * @param array 	$orderid_array 	排序字段值
	 * @param string 	$field 			排序的字段，默认为orderid
	 * @return void
	 */
	protected function _sort($model, $orderid_array, $field='orderid'){
		
		if($orderid_array){
			foreach($orderid_array as $id => $orderid){
				$orderid = is_numeric($orderid) ? $orderid : 0;
				$model->where(array('id'=>$id))->save(array($field=>$orderid));
			}
		}
	}
	
	
	/**
	 * 对表单ids[]进行批量删除
	 * 
	 * @param mixed 	$model 			模型对象
	 * @param array 	$id_list 		ids[]字段值
	 * @return void
	 */
	protected function _delete($model, $id_list){
		if($id_list){
			foreach($id_list as $id){
				$id = intval($id);
				if($id <= 0) continue;
				
				$model->where(array('id'=>$id))->delete(); // _after_delete()回调函数才可以调到
			}
		}
	}
	
	protected function _toggle_field($model, $id_list, $value='0', $field='is_show'){
		if($id_list){
			foreach($id_list as $id){
				$id = intval($id);
				if($id <= 0) continue;
				
				$model->where(array('id'=>$id))->save(array($field=>$value));
			}
		}
	}
	
	/**
	 * 推送到推荐位
	 * @param array	$id_list 			ids[]字段值
	 * @param array	$position_id_list	position_id[]推荐位ID数组
	 */
	protected function _push($id_list, $position_id_list){
		// 分别把产品推送到各个推荐位
		if($id_list){
			$data = array();
			foreach($id_list as $id){
				$data['item_id'] 	= $id;
				$data['channel_id'] = $this->_channel['id'];
				$data['module_id'] 	= $this->_channel['module_id'];
				$data['module'] 	= M('Module')->where(array('id'=>$this->_channel['module_id']))->getField('name');
				foreach($position_id_list as $position_id){
					$data['position_id'] = $position_id;
					
					if(M('PositionData')->where($data)->find()){
						continue;
					}
					
					$item = M($data['module'])->find($id);
					$data['data'] 		 = serialize($item);
					$data['create_time'] = time();
					
					$effect = M("PositionData")->add($data);
					unset($data['position_id'], $data['data'], $data['create_time']);
					
					// 推荐成功后，修改产品的是否推荐字段is_recommend为1
					if($effect){
						M($data['module'])->where(array('id'=>$id))->save(array('is_recommend'=>'1'));
					}
				}
			}
		}
	}
	
}
?>