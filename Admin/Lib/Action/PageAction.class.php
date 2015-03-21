<?php
/**
 +----------------------------------------------------------------------------
 * 单网页 控制器类
 +----------------------------------------------------------------------------
 * @category 内容管理模块(CMSPower内置)
 * @author fanrong33 <fanrong33#qq.com>
 * @version v1.0.3 Build 20140516
 +----------------------------------------------------------------------------
 */
class PageAction extends ContentParentAction {
	
	public function index(){
		$name = $this->getActionName();
	 	$model = D($name);
	 	
	 	if($this->isPost()){
	 		$_POST['title'] = trim($this->_post('title'));
	 		
	 		$_validate = array(
				array('title'	, 'require', '标题不能为空', 1),
				array('content'	, 'require', '内容不能为空', 1),
			);
			
	 		if(false === $data = $model->validate($_validate)->create()){
	 			$this->error($model->getError());
	 		}
	 		
	 		if($_POST['id']){
	 			$data['update_time'] = time();
		 		$effect = $model->save($data);
	 		}else{
		 		$data['channel_id']  = $this->_channel['id'];
	 			$data['create_time'] = time();
		 		$effect = $model->add($data);
	 		}
	 		if($effect){
	 			$this->success('修改成功');
	 		}else{
	 			$this->error('修改失败');
	 		}
	 	}
	 	
	 	// 获取单网页对象
	 	$cond = array();
	 	$cond['channel_id'] = $this->_channel['id'];
	 	$page = $model->where($cond)->find();
	 	
	 	$this->assign('_model', $page);
	 	$this->display();
	}
	
}
?>