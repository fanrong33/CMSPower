<?php
/**
 +----------------------------------------------------------------------------
 * 管理员视图 模型类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.0 Build 20130328
 +------------------------------------------------------------------------------
 */
class AdminViewModel extends ViewModel{
	
	public $viewFields = array(
		'Admin' => array('*', '_type'=>'LEFT'),
		'Role' => array('name'=>'role_name', 'description'=>'role_description', '_on'=>'Admin.role_id=Role.id'),
	);
	
}
?>