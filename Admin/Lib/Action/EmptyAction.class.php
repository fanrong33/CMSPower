<?php
/**
 +----------------------------------------------------------------------------
 * 空模块 控制器类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.1 Build 20130530
 +----------------------------------------------------------------------------
 */
class EmptyAction extends Action{

	public function index(){
		$module_name = MODULE_NAME;
		if(0 === strpos(MODULE_NAME, 'System')){
			// 业务规则:
			// SystemBase 对应 System/index/type/base，指向系统设置的基础配置

			 
			// 解析得到系统设置对应type
			$type = strtolower(substr(MODULE_NAME, 6)); // SystemBase -> base
			
			R('System/index', array($type));
		}
		
	}
	
}
?>