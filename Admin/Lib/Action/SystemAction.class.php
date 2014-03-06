<?php
/**
 +----------------------------------------------------------------------------
 * 系统设置 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.0.1 Build 20140217
 +------------------------------------------------------------------------------
 */
class SystemAction extends AdminCommonAction{
	
	public function index($type=''){
		$type = isset($_GET['type']) ? $_GET['type'] : $type;
		$type = strtolower(htmlspecialchars($type));
		
		$cond = array();
		$cond['name'] = 'system'.$type;
		$nav = D('Nav')->where($cond)->find();
		if(!$nav){
			$this->error('不存在该类别的系统设置');
		}
		
		$setting = require CONF_PATH.'setting.php';
		
		if($this->isPost()){
			$result = D("System")->edit($_POST['config'], $type);
			if($result){
				$this->success('保存成功');
			}else{
				$this->error('保存失败');
			}
		}

		$this->assign('_type', $nav['title']);
		$this->assign('_model', D("System")->getConfig($type));
		$this->assign('settings', $setting[$type]);
		$this->display('System:index');	
	}
		
}
?>