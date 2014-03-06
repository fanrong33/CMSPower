<?php
/**
 +----------------------------------------------------------------------------
 * 网址视图 模型类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.0 Build 20140105
 +------------------------------------------------------------------------------
 */
class UrlViewModel extends ViewModel{
	
	public $viewFields = array(
		'Url' 		=> array('*', '_type'=>'LEFT'),
		'Category' 	=> array('name'=>'category_name', '_on'=>'Url.category_id=Category.id'),
	);
	
}
?>