<?php
/**
 +----------------------------------------------------------------------------
 * 推荐位管理 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.0.0 Build 20130911
 +------------------------------------------------------------------------------
 */
class PositionAction extends AdminCommonAction{
	
	/**
	 * 推荐位管理
	 */
	public function index(){
		$model = D("PositionView");
		
		// 列表过滤器，生成查询Map对象
		$map = $this->_search();
		if(method_exists($this, '_filter')){
			$this->_filter($map);
		}
		$this->_list($model, $map, array('orderid', 'id'), array('asc', 'desc'));
		
		$this->display();
	}
	
	
	/**
	 * 保存排序
	 */
	public function sort(){
		if($this->isPost()){
			if($_POST['orderid']){
				// 保存排序
				$this->_sort(M("Position"), $_POST['orderid']);
			}
			$this->ajaxReturn('', '保存排序成功', 1);
		}
	}
	
	
	/**
	 * 添加推荐位
	 */
	public function add(){
		$model = D("Position");
		if($this->isPost()){
			
			$_POST['name'] = trim($this->_post('name'));
			
			$_validate = array(
				array('name'			, 'require', '推荐位名称不能为空'),
				array('module_id'		, 'require', '请选择所属模型'),
				array('max_number'		, 'require', '最大保存条数不能为空'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			$data['orderid'] = 999999;
			$data['create_time'] = time();
			
			$insert_id = $model->add($data);
			if($insert_id > 0){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		
		// 获取模型列表
		$module_list = D("Module")->order('id asc')->select();
		
		$this->assign('module_list', $module_list);
		$this->display('edit');
	}
	
	
	/**
	 * 修改推荐位
	 */
	public function edit(){
		
		$model = D("Position");
		
		if($this->isPost()){
			
			$_POST['name'] = trim($this->_post('name'));
			
			$_validate = array(
				array('name'			, 'require', '推荐位名称不能为空'),
				array('module_id'		, 'require', '请选择所属模型'),
				array('max_number'		, 'require', '最大保存条数不能为空'),
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
			$this->error('参数id不能为空');
		}
		$position = $model->find($id);
		if(!$position){
			$this->error('推荐位不存在');	
		}
		
		// 获取模型列表
		$module_list = D("Module")->order('id asc')->select();
		
		$this->assign('module_list', $module_list);
		$this->assign('_model', $position);
		$this->display();
	}
	
	
	/**
	 * 批量删除推荐位
	 */
	public function delete(){
		$model = D('Position');
		
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
	
	
	/**
	 * 推荐位信息管理
	 */
	public function public_item(){
		$model = D("PositionData");
		
		$position_id = intval($_GET['position_id']);
		if(!$position_id){
			$this->error('参数position_id不能为空');
		}
		// 列表过滤器，生成查询Map对象
		$map = $this->_search();
		$map['position_id'] = $position_id;
		$list = $this->_list($model, $map, array('orderid', 'id'), array('asc', 'desc'));
		$tmp_list = array();
		foreach($list as $rs){
			$rs['data'] = unserialize($rs['data']);
			$tmp_list[] = $rs;
		}
		$list = $tmp_list;
		unset($tmp_list);
		
		// 获取栏目HashMap
		$channel_list = D("Channel")->field('id,title')->select();
		$channel_map = array_key_list($channel_list, 'id');
		
		$this->assign('list', $list);
		$this->assign('channel_map', $channel_map);
		$this->display();
	}
	
	
	/**
	 * 信息管理-保存排序
	 */
	public function public_item_sort(){
		if($this->isPost()){
			if($_POST['orderid']){
				// 保存排序
				$this->_sort(M("PositionData"), $_POST['orderid']);
			}
			$this->ajaxReturn('', '保存排序成功', 1);
		}
	}
	
	
	/**
	 * 信息管理-批量删除推荐位
	 */
	public function public_item_delete(){
		$model = D('PositionData');
		
		if($this->isPost()){
			// 批量删除
			if($_POST['ids']){
				foreach($_POST['ids'] as $id){
					$id = intval($id);
					if($id <= 0) continue;
					
					$position_data = $model->find($id);
					$effect = $model->delete($id);
					if($effect){
						// 每次在信息管理中移除推荐的产品后，都要判断是否不存在该产品的推荐了，然后修改产品表中的 is_recommend 为0，去掉推荐标识
						$cond = array();
						$cond['item_id'] = $position_data['item_id'];
						$cond['module']  = $position_data['module'];
						if(!$model->where($cond)->find()){
							M($position_data['module'])->where(array('id'=>$position_data['item_id']))->save(array('is_recommend'=>'0'));
						}
					}
				}
			}
			$this->ajaxReturn('', '批量删除成功', 1);
		}
	}
}
?>