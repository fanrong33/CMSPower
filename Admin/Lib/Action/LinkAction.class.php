<?php
/**
 +----------------------------------------------------------------------------
 * 友情链接管理 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33, xiaoxin
 * @version v1.0.5 Build 20140226
 +------------------------------------------------------------------------------
 */
class LinkAction extends AdminCommonAction{
	
	/**
	 * 查询链接
	 */
	public function index(){
		$is_show = $this->_get('is_show', 'trim', '1');
		$keyword = $this->_get('keyword', 'trim', '');
		
		$name = $this->getActionName();
		$model = D($name);

		$map = array();
		$map['is_show'] = $is_show;
		if($keyword != ''){
			if(!is_utf8($keyword)){
				$keyword = iconv("gb2312", "utf-8", $keyword);
			}
			$where['title']  = array('like', "%$keyword%");
			$where['url'] 	 = array('like', "%$keyword%");
			$where['_logic'] = 'or';
			$map['_complex'] = $where; 
		}
		$this->_list($model, $map, array('orderid', 'id'), array('asc', 'desc'));
		
		// 计算总数
		$show_count = $model->where(array('is_show'=>'1'))->count();
		$hide_count = $model->where(array('is_show'=>'0'))->count();
		
		$this->assign('is_show', $is_show);
		$this->assign('keyword', $keyword);
		$this->assign('show_count', $show_count);
		$this->assign('hide_count', $hide_count);
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
	 * 添加链接
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			$_POST['url'] 	= trim($this->_post('url'));
			
			$_validate = array(
				array('title'	, 'require', '链接标题不能为空'),
				array('title'	, '', '链接标题已存在', 1, 'unique'),
				array('url'		, 'require', '链接地址不能为空'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			if($data['url'] && strpos($data['url'], 'http://') === false){
				$data['url'] = 'http://'.$data['url'];
			}
			$data['is_show'] 	= '1';
			$data['orderid'] 	= 0;
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
	 * 修改链接
	 */
	public function edit(){
		$name = $this->getActionName();
		$model = D($name);
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			$_POST['url'] 	= trim($this->_post('url'));
			
			$_validate = array(
				array('title'	, 'require', '链接标题不能为空'),
				array('title'	, '', '链接标题已存在', 1, 'unique'),
				array('url'		, 'require', '链接地址不能为空'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			if($data['url'] && strpos($data['url'], 'http://') === false){
				$data['url'] = 'http://'.$data['url'];
			}
			$effect = $model->save($data);
			if($effect){
				$this->success('修改成功');
			}else{
				$this->success('修改失败');
			}
		}
		
		$id = $this->_get('id', '参数id不能为空');
		
		$link= $model->find($id);
		if(!$link) $this->error('友情链接不存在');
		
		$this->assign('_model', $link);
		$this->display();
	}
	
	/**
	 * 删除链接
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
			$id = $this->_get('id', '参数id不能为空');
			
			$effect = $model->delete($id);
			if($effect){
				$this->success('删除成功');
			}
		}
	}
	
	/**
	 * 上架下架
	 */
	public function toggle(){
		$is_show = $this->_get('is_show');
		
		if($this->isPost()){
			// 批量下架上架
			if($_POST['ids']){
				$this->_toggle_field(D('Link'), $_POST['ids'], $is_show, 'is_show');
			}
			$this->ajaxReturn('', '更新成功', 1);
		}
	}
	
	
}
?>