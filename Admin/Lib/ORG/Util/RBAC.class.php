<?php
/**
 +----------------------------------------------------------------------------
 * 基于角色的数据库方式验证 类
 +----------------------------------------------------------------------------
 * @author fanrong33 <fanrong33#qq.com>
 * @version v2.2.2 Build 20140307
 +------------------------------------------------------------------------------
 */
class RBAC{
	
	/**
	 * 判断是否拥有后台应用的访问权限
	 * @param int $role_id 管理员拥有的角色ID
	 * @return boolean
	 */
	public static function accessRight($role_id){
		
		/**
		 * 业务规则：
		 * 角色表只存储拥有权限的资源；没有权限的资源拒绝访问；不在资源列表中的未控制资源则为白名单
		 * 
		 * 所以所有的权限资源列表都要在access_list里面，没有的has_right=0，有的才has_right=1，不在access_list里的资源则为白名单
		 * 设计最简单的方式，来验证权限。增加是否拥有权限属性has_right
		 */
		// 检查是否需要认证
		if(self::checkAccess()){
			
			// 获取后台管理员权限角色所拥有的权限
//			$role = D("Role")->find($role_id);
			$role = cache_get('Role', $role_id, 60*10);
			$has_resource_array = unserialize($role['resources']);
			/**
			 * output:
			 *	$has_resource_array = array(
			 *		'Member' => array(
			 *			'index' => array('has_right'=>1),
			 *			'add'	=> array('has_right'=>1),
			 *			'edit'	=> array('has_right'=>1),
			 *			'delete'=> array('has_right'=>1),
			 *      ),
			 *	);
			 */
			
			if($role['type'] == '1') return true;	// 超级管理员
			if($role['type'] == '2') return false;	// 禁止访问

			$cache_key = generate_cache_key('access_list', $role_id);
			$access_list = S($cache_key);
			
			if(!$access_list){
				// 自定义的权限资源访问列表
				$access_list = array();
				
				// 获取所有后台应用资源列表
				$resource_list = self::getNavResoureList();
				
				// 获取应用的控制器方法
				$methods_list = D("Resource")->select();
				$tmp = array();
				foreach($methods_list as $rs){
					$rs['methods'] = explode('|', $rs['methods']);
					$tmp[] = $rs;
				}
				$methods_list = $tmp;
				$methods_list = array_key_list($methods_list, 'name');
				
				foreach($resource_list as $nav){ // nav应用
					$nav_name = $nav['name'];
					$methods = $methods_list[$nav_name]['methods'];
					foreach($methods as $method){
						list($action_name, $action_title) = explode(':', $method);
						
						$access_list[$nav_name][$action_name] = array('has_right'=>0);
					}
				}
				
				
				// 1.使用自己的权限覆盖access_list
				foreach($has_resource_array as $nav_name => $methods){
					foreach($methods as $method => $has_right){
						$access_list[$nav_name][$method]['has_right'] = $has_right['has_right'];
					}
				}
				
				// 2.使用nav应用的禁用disabled=1,来覆盖access_list, 设置为没有权限
				foreach($resource_list as $nav_name => $nav){
					// disabled=1，禁用的后台应用，不允许访问
					if($nav['disabled'] == '1'){ 
						foreach($access_list[$nav_name] as $method => $has_right){
							$access_list[$nav_name][$method]['has_right'] = 0;
						}
					}
				}
				S($cache_key, $access_list, 60*10);
			}
//			dump($access_list);
			
			
	        $module = MODULE_NAME;
	        $action = ACTION_NAME;
	        
        	// 未在权限资源里面进行控制，白名单模式
	        if(!isset($access_list[$module][$action])) return true;
	        
	        if(!$access_list[$module][$action]['has_right']) return false;
		}
		return true;
	}
	
	
	/**
	 * 判断是否拥有后台栏目内容模块的访问权限
	 * @param int $role_id 		管理员拥有的角色ID
	 * @param int $channel_id 	要访问的栏目内容ID
	 * @return boolean
	 */
	public static function accessRightContent($role_id, $channel_id){
		
		/**
		 * 业务规则：
		 * 角色表只存储拥有访问权限的栏目资源ID；没有权限的栏目资源拒绝访问；没有白名单的概念
		 * 
		 * 设计最简单的方式，来验证权限
		 */
		

		// 获取后台管理员权限角色所拥有的权限
		$role = D("Role")->find($role_id);
		$has_channel_array = unserialize($role['channels']);
		/**
		 * output:
		 *	$has_resource_array = array(
		 *		'10' => array('has_right'=>1),
		 *		'11' => array('has_right'=>1),
		 *	);
		 */
		
		if($role['type'] == '1') return true;	// 超级管理员
		if($role['type'] == '2') return false;	// 禁止访问
		
		if(!isset($has_channel_array[$channel_id]['has_right'])) return false;
		
		return true;
	}
	
	
	/**
	 * 根据拥有的后台应用资源权限，反向组装得到nav tree
	 */
	public static function getRoleNavTree($role_id){
//		TODO 若对整个结果缓存，（包括active=true），就会出现错误，当点击不同的navbar，active还是旧的，导致权限出错
//		$cache_key = generate_cache_key('role_nav_tree', $role_id);
//		$nav_tree = S($cache_key);
//		if(!$nav_tree){
			// 1. 获取后台管理员权限角色所拥有的权限（不存储pid，因为那些都是灵活可配置的，会过时）
			$role = D("Role")->find($role_id);
			$has_resource_array = unserialize($role['resources']);
			$has_channel_array  = unserialize($role['channels']);
			$channel_list = D("Channel")->select();
			$has_channel_right = false; // 拥有栏目资源权限
			foreach($channel_list as $rs){
				if(isset($has_channel_array[$rs['id']]['has_right'])){
					$has_channel_right = true;
					break;
				}
			}
			
			// 2. 获取所有nav元素资源列表
			$all_nav_list = self::getAllResourceList();
			
			$active_navbar_id = 0;
			foreach($all_nav_list as $rs){
				// 判断当前MODULE_NAME的nav设置active=true
				if($rs['type'] == 'nav' && $rs['name'] == MODULE_NAME){
					$active_navbar_id = $rs['pid'];
					break;
				}
			}		
			
			// 3. 根据拥有的权限，智能计算需要显示的拥有权限的navbar导航栏nav
			$tmp = array();
			
			foreach($all_nav_list as $rs){
				if($rs['type'] == 'nav'){
					// 拥有index查看权限，才显示app应用标签
					if(isset($has_resource_array[$rs['name']]['index'])){
						$rs['has_right'] = 1;
					}
				}
				if($rs['id'] == $active_navbar_id){
					$rs['active'] = true;
				}
				$tmp[] = $rs;
			}
			$all_nav_list = $tmp;
			unset($tmp);
			
			$nav_tree = list_to_tree($all_nav_list);
			if($role['type'] == '1') return $nav_tree; // 超级管理员
			
			
			// 3.1 过滤显示拥有应用权限的navbar
			$tmp = array();
			foreach($nav_tree as $navbar){
				if(isset($navbar['_child'])){
					foreach($navbar['_child'] as $nav){
						if($nav['has_right']==1){
							$tmp[$navbar['id']] = $navbar; // 以id为索引，保证只添加一次
							continue;
						}
						
						if($nav['type'] == 'header' && $nav['title'] == '内容管理' && $has_channel_right){
							$tmp[$navbar['id']] = $navbar; // 以id为索引，保证只添加一次
						}
					}
				}
			}
			$nav_tree = $tmp;
			unset($tmp);
			
			
			// 3.2 过滤显示前面一次的header,[xxx应用]后面的divider
			$tmp = array();
			$nav_child_tmp = null;	// 临时nav['_child'], 用于转换_child
			$before_header = null;	// 临时 header
			$last_nav_type = null;	// 上一次nav类型
			foreach($nav_tree as $navbar){
				if(isset($navbar['_child'])){
					foreach($navbar['_child'] as $nav){
						
						// 如果存在栏目资源权限，则显示内容CONTENT???
						if($nav['title'] == '内容管理' && $has_channel_right){
							$nav_child_tmp[] = $nav;
							$last_nav_type = 'channel_tree';
						}
						
						if($nav['type'] == 'header'){
							$before_header = $nav;
						}
						
						if($nav['type'] == 'nav' && $nav['has_right']){
							if(null !== $before_header){
								$nav_child_tmp[] = $before_header;
								$before_header = null;
							}
							$nav_child_tmp[] = $nav;
							$last_nav_type = 'nav';
						}
						
						if($nav['type'] == 'divider'){
							if(!is_null($last_nav_type) && $last_nav_type != 'divider'){
								$nav_child_tmp[] = $nav;
								$last_nav_type = 'divider';
							}
						}
					}
					
					unset($navbar['_child']);
					$navbar['_child'] = $nav_child_tmp;
					$tmp[] = $navbar;
					$nav_child_tmp = null;
				}
			}
			$nav_tree = $tmp;
			unset($tmp);
			
//			S($cache_key, $nav_tree, 60*10);
//		}
		
		return $nav_tree;
	}
	
	
	/**
	 * 根据拥有的后台栏目内容资源权限，反向组装得到channel tree
	 * @param int $role_id 	管理员拥有的角色ID
	 * @return array
	 */
	public static function getRoleChannelTree($role_id){
		
		// 获取栏目树状列表,关联获取得到栏目所属模型
		$channel_table  = D("Channel")->getTableName();
		$module_table	= D("Module")->getTableName();
		$sql = <<<EOF
select a.*,b.name as module_name,b.title as module_title 
from $channel_table a left join $module_table b 
on a.module_id=b.id 
order by orderid asc 
EOF;
		
		$channel_list = D("Channel")->query($sql);
		// 去掉没有权限的栏目
		
		$role = D("Role")->find($role_id);
		// “超级管理员”的情况,不进行过滤，返回全部
		if($role['type'] == '3'){
			$has_channel_array = unserialize($role['channels']);
			
			$tmp = array();
			foreach($channel_list as $rs){
				if(isset($has_channel_array[$rs['id']]['has_right'])){
					$tmp[] = $rs;
				}
			}
			$channel_list = $tmp;
			unset($tmp);
		}
		
		
		// 得到拥有权限的栏目树
		$channel_tree = list_to_tree($channel_list);
		return $channel_tree;
	}
	
	
	/**
	 * 获取所有后台应用资源列表（在资源列表中进行控制的）
	 */
	private static function getNavResoureList(){
		$cond = array();
		$cond['type'] = 'nav';
    	$resource_list = D("Nav")->where($cond)->order('orderid asc')->select();
    	
    	// 重新组装list，过滤字段，只保留name,title,简化，集中注意力在核心上
    	$tmp = array();
    	foreach($resource_list as $rs){
    		$tmp[$rs['name']] = array(
    			'name' 		=> $rs['name'],
    			'title' 	=> $rs['title'],
    			'pid'		=> $rs['pid'],
    			'disabled' 	=> $rs['disabled'], // disabled=1，禁用的后台应用，不允许访问
    		);
    	}
    	$resource_list = $tmp;
		unset($tmp);
		return $resource_list;
	}
	
	
	/**
	 * 获取所有资源列表
	 */
	private static function getAllResourceList(){
    	$nav_list = D("Nav")->order('orderid asc')->select();
    	
		return $nav_list;
	}
	
	
	/**
	 * 检查当前操作是否需要认证
	 */
	private static function checkAccess(){
		return true;
	}
	
	
}
?>