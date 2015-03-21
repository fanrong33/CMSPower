<?php
/**
 +----------------------------------------------------------------------------
 * 广告视图 模型类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.0 Build 20130319
 +------------------------------------------------------------------------------
 */
class AdViewModel extends ViewModel{
	
	public $viewFields = array(
		'Ad' 			=> array('*', '_type'=>'LEFT'),
		'AdPosition' 	=> array('name'=>'ad_position_name', 'title'=>'ad_position_title', 'description'=>'ad_position_description', '_on'=>'Ad.ad_position_id=AdPosition.id'),
	);
	
}
?>