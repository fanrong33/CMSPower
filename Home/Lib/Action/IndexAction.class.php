<?php
/**
 +----------------------------------------------------------------------------
 * 前台首页 控制器类
 +----------------------------------------------------------------------------
 * @category 前台应用
 * @author fanrong33
 * @version v1.0.1 Build 20140112
 +------------------------------------------------------------------------------
 */
class IndexAction extends CommonAction {
	
    public function index(){
    	echo 'Hello CMSPower';
    	exit;
		//$this->display();
    }
    
}