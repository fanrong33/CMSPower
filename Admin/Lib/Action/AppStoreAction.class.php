<?php
/**
 +----------------------------------------------------------------------------
 * 在线应用 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.0.1 Build 20140124
 +------------------------------------------------------------------------------
 */
class AppStoreAction extends AdminCommonAction{
	
	/**
	 * 查询在线应用
	 */
	public function index(){
		$keyword = trim($_GET['keyword']);
		if(!is_utf8($keyword)){
			$keyword = iconv('gb2312', 'utf-8', $keyword);
		}
		$model = D("AppStore");
		
		$map = array();
		if($keyword){
			$where['name']   = array('like', "%$keyword%");
			$where['title']  = array('like',"%$keyword%");
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
		if($this->isPost()){
			if($_POST['orderid']){
				$this->_sort(M("AppStore"), $_POST['orderid']);
			}
			$this->ajaxReturn('', '保存排序成功', 1);
		}
	}
	
	/**
	 * 添加在线应用
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			$_POST['name'] = trim($this->_post('name'));
			
			$_validate = array(
				array('name', 'require', '请输入应用名称'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			$data['file_size']   = get_file($data['zip_url_file_id'], 'size');
			$data['update_time'] = time();
			$insert_id = $model->add($data);
			if($insert_id > 0){
				$this->success('添加成功', cookie('_currentUrl_'));
			}else{
				$this->error('添加失败');
			}
		}
		
		$this->display('edit');
	}
	
	
	/**
	 * 修改在线应用
	 */
	public function edit(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			$_POST['name'] = trim($this->_post('name'));
			
			$_validate = array(
				array('name', 'require', '请输入应用名称'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			$data['file_size']   = get_file($data['zip_url_file_id'], 'size');
			$data['update_time'] = time();
			
			$effect = $model->save($data);
			if($effect){
				$this->success('修改成功');
			}else{
				$this->success('修改失败');
			}
		}
		
		$id = $this->_get('id', '参数id不能为空');
		$app = $model->find($id);
		if(!$app){
			$this->error('该在线应用不存在');	
		}
		
		$this->assign('_model', $app);
		$this->display();
	}
	
	
	/**
	 * 删除在线应用
	 */
	public function delete(){
		$name = $this->getActionName();
		$model = D($name);
		
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('参数id不能为空');
		}
		
		// 不允许删除id为1的创始人
		if($id > 1){
			$effect = $model->delete($id);
			if($effect){
				$this->success('删除成功');
			}
		}
	}
	
}

?>