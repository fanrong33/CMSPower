<?php
/**
 +----------------------------------------------------------------------------
 * 文章 控制器类
 +----------------------------------------------------------------------------
 * @category 内容管理模块(CMSPower内置)
 * @author fanrong33, funwee <fanrong33#qq.com>
 * @version v1.1.5 Build 20140306
 +----------------------------------------------------------------------------
 */
class ArticleAction extends ContentParentAction {
	
	/**
	 * 文章管理
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function index(){
		$keyword = $this->_get('keyword', 'trim', '');
		
		$name = $this->getActionName();
	 	$model = D($name);
	 	
	 	// 获取文章列表
	 	$map = array();
	 	$map['channel_id'] = $this->_channel['id'];
	 	if($keyword != ''){
	 		if(!is_utf8($keyword)){
	 			$keyword = iconv("gb2312", "utf-8", $keyword);
	 			$map['title'] = array('like', "%$keyword%");
	 		}
	 	}
	 	$this->_list($model, $map, array('orderid', 'id'), array('asc', 'desc'));
	 	
	 	$this->assign('keyword'	, $keyword);
	 	$this->assign('_channel', $this->_channel);
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
	 * 添加文章
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
	 	
	 	if($this->isPost()){
	 		
	 		$_POST['title'] = trim($this->_post('title'));
	 		
	 		$_validate = array(
				array('title'	, 'require', '文章标题不能为空', 1),
				array('content'	, 'require', '文章内容不能为空', 1),
			);
			
	 		if(false === $data = $model->validate($_validate)->create()){
	 			$this->error($model->getError());
	 		}
	 		$data['channel_id']  = $this->_channel['id'];
 			$data['create_time'] = time();
	 		$insert_id = $model->add($data);
	 		if($insert_id){
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
	 * 修改文章
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function edit(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			
			$_validate = array(
				array('title'	, 'require', '文章标题不能为空', 1),
				array('content'	, 'require', '文章内容不能为空', 1),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			$data['update_time'] = time();
			$effect = $model->save($data);
			if($effect){
				$this->success('修改成功');
			}else{
				$this->success('修改失败');
			}
		}
		
		$id = intval($_GET['id']);
		$article = $model->find($id);
		if(!$article) {
			$this->error('文章不存在');	
		}
		
		$this->assign('_model', $article);
		$this->display();
	}
	
	/**
	 * 删除文章
	 * @author fanrong33 <fanrong33#qq.com>
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
	
	/**
	 * 推送信息到推荐位
	 */
	public function push(){
		if($this->isPost()){
			if($_POST['ids']){
				// 推送信息到各个推荐位
				$this->_push($_POST['ids'], $_POST['position_id']);
			}
			$this->ajaxReturn('', '推送成功', 1);
		}
	}

}

/**
 * TODO
 * 1、发布文章支持绑定到分类
 * 2、文章列表支持按分类搜索
 */
?>