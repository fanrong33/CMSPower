<?php
/**
 +----------------------------------------------------------------------------
 * 推荐位视图 模型类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.0 Build 20130911
 +------------------------------------------------------------------------------
 */
class PositionViewModel extends ViewModel{
	
	public $viewFields = array(
		'Position' 	=> array('*', '_type'=>'LEFT'),
		'Module' 	=> array('title'=>'module_title', '_on'=>'Position.module_id=Module.id'),
	);
	
}
?>