<?php
/**
 +----------------------------------------------------------------------------
 * 内容模块父类 控制器类
 * 内容管理模块 需继承自 ContentParentAction
 +----------------------------------------------------------------------------
 * @category 内容管理模块父类
 * @author fanrong33 <fanrong33#qq.com>
 * @version v1.2.0 Build 20140306
 +------------------------------------------------------------------------------
 */
class ContentParentAction extends AdminCommonAction {
	
	/**
	 * 内容模块对应的栏目
	 */
	protected $_channel = null;
	protected $_channel_path = '';
	
	public function _initialize(){
		parent::_initialize();
		
	 	$ch = trim($_GET['ch']);
	 	if(!$ch){
	 		$this->error('栏目参数未设置');	
	 	}
	 	
	 	// 控制内容模块栏目的权限
	 	import('@.ORG.Util.RBAC');
	 	$admin = D("Admin")->find($this->_admin_id);
	 	
		if(!RBAC::accessRightContent($admin['role_id'], $this->_channel['id'])){
			$this->error('没有权限，拒绝访问');
		}
		
	 	// 获取栏目，设为ContentParent变量
	 	$this->_channel = D("Channel")->where(array('name'=>$ch))->find();
	 	
	 	$this->_channel_path = "[ {$this->_channel['title']} ]"; // [ 关于 > 公司介绍 ]
	 	$pid = $this->_channel['pid'];
	 	if($pid != 0){
	 		$parent_channel = D("Channel")->find($pid);
	 		$this->_channel_path = substr_replace($this->_channel_path, "{$parent_channel['title']} > ", 2, 0);
	 	}
	 	
	 	// 设置内容模块的栏目路径
	 	$this->assign('_channel_path', $this->_channel_path);
	 	
	 	if($parent_channel){
	 		// 获取当前栏目的最高栏目，并将zTree设置为open
	 		$channel_tree = $this->get('_channel_tree');
	 		foreach($channel_tree as $key => $rs){
	 			if($channel_tree[$key]['id'] == $parent_channel['id']){
	 				$channel_tree[$key]['open'] = true;
	 			}
	 		}
	 		$this->assign('_channel_tree'	, $channel_tree);	// 内容栏目树
	 	}
	 	$this->assign('_channel', $this->_channel);
	}
	
}
?>