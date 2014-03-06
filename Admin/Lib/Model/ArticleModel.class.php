<?php
/**
 +----------------------------------------------------------------------------
 * 文章内容模块 模型类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.0 Build 20130912
 +------------------------------------------------------------------------------
 */
class ArticleModel extends Model{
	
	protected function _after_delete($data,$options) {
		
		// 删除时同时删除关联的推送信息
		$cond = array();
		$cond['item_id'] = $options['where']['id'];
		$cond['module'] = 'Article';
		D("PositionData")->where($cond)->delete();
	}
	
}	
?>