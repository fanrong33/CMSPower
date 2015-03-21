<?php
/**
 +----------------------------------------------------------------------------
 * 推荐位 模型类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.0 Build 20130912
 +------------------------------------------------------------------------------
 */
class PositionModel extends Model{
	
	// 在更新时删除内存缓存
	protected function _after_update($data, $options) {
		
	}

	protected function _after_delete($data, $options) {
		
		// 删除时同时删除推荐位下的推荐信息 
		$cond = array();
		$cond['position_id'] = $options['where']['id'];
		D("PositionData")->where($cond)->delete();
	}
	
}	
?>