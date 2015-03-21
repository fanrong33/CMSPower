<?php
/**
 +----------------------------------------------------------------------------
 * 控制台iframe框架 控制器类
 * 后台应用不使用缓存，减少系统的复杂性，保证实时性
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.2.0 Build 20130522
 +------------------------------------------------------------------------------
 */
class IndexAction extends AdminCommonAction {
	
    public function index(){
    	
    	if($this->_admin_id){
    		// 已登录，跳转到AboutUs/index
    		U('AboutUs/index', array(), true, true);
    	}
    	
    	
    	// 获取管理员信息，左关联角色名
    	$admin_table 	= D("Admin")->getTableName();
    	$role_table 	= D("Role")->getTableName();
    	$sql = <<<EOF
select a.*, b.name as role_name
from $admin_table a left join $role_table b
on a.role_id=b.id
where a.id=$this->_admin_id limit 1
EOF;
		$admin = D("Admin")->query($sql);
		$admin = $admin[0];

    	
    	import('@.ORG.Util.RBAC');

    	$channel_tree 	= RBAC::getRoleChannelTree($admin['role_id']);
		$nav_tree 		= RBAC::getRoleNavTree($admin['role_id']);
    	
    	$this->assign('_nav_tree', $nav_tree); // 导航栏和导航对象
    	$this->assign('_channel_tree', $channel_tree); // 内容栏目树
    	$this->assign('_admin', $admin);
    	$this->display();
    }
    
}