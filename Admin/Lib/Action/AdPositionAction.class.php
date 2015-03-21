<?php
/**
 +----------------------------------------------------------------------------
 * 广告位管理 控制器类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.1.3 Build 20140225
 +------------------------------------------------------------------------------
 */
class AdPositionAction extends AdminCommonAction{
	
	/**
	 * 查询广告位
	 */
	public function index(){
		$keyword = $_GET['keyword'] ? htmlspecialchars(trim($_GET['keyword'])) : '';
		
		$name = $this->getActionName();
		$model = D($name);
		
		$map = array();
		if($keyword){
			$where['name']   = array('like', "%$keyword%");
			$where['title']  = array('like', "%$keyword%");
			$where['_logic'] = 'or';
			$map['_complex'] = $where;
			$this->assign('keyword', $keyword);
		}
		$this->_list($model, $map, array('orderid', 'id'), array('asc', 'desc'));

		$this->display();
	}
	
	
	/**
	 * 保存排序
	 */
	public function sort(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			if($_POST['orderid']){
				// 保存排序
				$this->_sort($model, $_POST['orderid']);
			}
			$this->ajaxReturn('', '保存排序成功', 1);
		}
	}
	
	
	/**
	 * 添加广告位
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			$_POST['name'] 	= trim($this->_post('name'));
			
			$_validate = array(
				array('title'	, 'require'	, '广告位名称不能为空'),
				array('name'	, 'require'	, '英文名不能为空'),
				array('width'	, 'require'	, '图片宽度不能为空'),
				array('height'	, 'require'	, '图片高度不能为空'),
				array('name'	, ''		, '该英文名已被使用', 1, 'unique'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			$data['orderid'] = 0;
			$data['create_time'] = time();
			
			$insert_id = $model->add($data);
			if($insert_id > 0){
				// "保存并继续添加"
				if($_POST['submit_continue']){
					$this->success('添加成功');
				}else{
					$this->success('添加成功', cookie('_currentUrl_'));
				}
			}else{
				$this->error('添加失败');
			}
		}
		
		$this->display('edit');
	}
	
	
	/**
	 * 修改广告位
	 */
	public function edit(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			$_POST['name']	= trim($this->_post('name'));
			$_POST['title'] = trim($this->_post('title'));
			
			$_validate = array(
				array('title'	, 'require'	, '广告位名称不能为空'),
				array('name'	, 'require'	, '英文名不能为空'),
				array('width'	, 'require'	, '图片宽度不能为空'),
				array('height'	, 'require'	, '图片高度不能为空'),
				array('name'	, ''		, '该英文名已被使用', 1, 'unique'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			$effect = $model->save($data);
			if($effect){
				$this->success('修改成功');
			}else{
				$this->success('修改失败');
			}
		}
		
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('id参数为空');
		}
		
		$ad_position = $model->find($id);
		if(!$ad_position){
			$this->error('广告位不存在');
		}

		$this->assign('_model', $ad_position);
		$this->display();
	}
	
	
	/**
	 * 删除广告位
	 */
	public function delete(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			// 批量删除
			if($_POST['ids']){
				$this->_delete($model, $_POST['ids']);
			}
			$this->ajaxReturn('', '批量删除成功', 1);
		}else{
			// 单个删除
			$id = intval($_GET['id']);
			if(!$id){
				$this->error('参数id不能为空');
			}
			
			$effect = $model->delete($id);
			if($effect){
				$this->success('删除成功');
			}
		}
	}
	
}
?>