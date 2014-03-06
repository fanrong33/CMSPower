<?php
/**
 +----------------------------------------------------------------------------
 * 广告管理 控制器类
 * 更改Ad名称为Sw（思维），防止被AdBlock插件拦截
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.5.0 Build 20140225
 +------------------------------------------------------------------------------
 */
class SwAction extends AdminCommonAction{
	
	/**
	 * 广告管理
	 */
	public function index(){
		$ad_position_id = $_GET['ad_position_id'] ? $this->_get('ad_position_id') : '';
		$is_show  		= isset($_GET['is_show']) ? $this->_get('is_show') : '1';
		$is_expire		= isset($_GET['is_expire']) ? $this->_get('is_expire') : '';
		$keyword  		= $_GET['keyword'] ? trim($this->_get('keyword')) : '';
		
		$model = D("AdView");
		
		$cond = array();
		$cond['is_show'] 	= $is_show;
		$cond['start_date'] = array('elt', date('Y-m-d'));
		$cond['_string'] 	= "end_date is NULL OR end_date >= '".date('Y-m-d')."'";
		// 如果为过期的，则不考虑显示隐藏
		if($is_expire == 1){
			$is_show = '';
			unset($cond['is_show']);
			unset($cond['start_date']);
			unset($cond['_string']);
			$cond['end_date'] = array('lt', date('Y-m-d'));
		}
		
		if($ad_position_id){
			$cond['ad_position_id'] = $ad_position_id;
		}
		if($keyword){
			if(!is_utf8($keyword)){
				$keyword = iconv('gb2312', 'utf-8', $keyword);
			}
			$cond['title'] = array('like', "%$keyword%");
		}
		$this->_list($model, $cond, array('orderid', 'id'), array('asc', 'desc'));
		
		
		// 获取显示(未过期)的广告数量、隐藏(未过期)的广告数量、过期的广告数量。不考虑过滤搜索条件，是总的显示数
		$this->get_count();		
		
		// 获取广告位列表
		$ad_position_list = D("AdPosition")->field('id,title')->order('orderid asc')->select();
		
		$this->assign('ad_position_id'	, $ad_position_id);
		$this->assign('is_show'			, $is_show);
		$this->assign('is_expire'		, $is_expire);
		$this->assign('keyword'			, $keyword);
		$this->assign('ad_position_list', $ad_position_list);
		$this->display();
	}
	
	/**
	 * 获取显示(未过期)的广告数量、隐藏(未过期)的广告数量、过期的广告数量。不考虑过滤搜索条件，是总的显示数
	 */
	private function get_count(){
		$model = D("Ad");
		
		$cond = array();
		$cond['is_show'] = '1';
		$cond['start_date'] = array('elt', date('Y-m-d'));
		$cond['_string'] = "end_date is NULL OR end_date >= '".date('Y-m-d')."'";
		$show_count = $model->where($cond)->count();
		
		$cond['is_show'] = '0';
		$hide_count = $model->where($cond)->count();
		
		unset($cond['is_show']);
		unset($cond['start_date']);
		unset($cond['_string']);
		$cond['end_date'] = array('lt', date('Y-m-d'));
		$expire_count = $model->where($cond)->count();
		
		$this->assign('show_count', $show_count);
		$this->assign('hide_count', $hide_count);
		$this->assign('expire_count', $expire_count);
	}
	
	/**
	 * 保存排序
	 */
	public function sort(){
		if($this->isPost()){
			if($_POST['orderid']){
				// 保存排序
				$this->_sort(M("Ad"), $_POST['orderid']);
			}
			$this->ajaxReturn('', '保存排序成功', 1);
		}
	}
	
	
	/**
	 * 添加广告
	 */
	public function add(){
		$model = D("Ad");
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			if(trim($_POST['start_date']) == ''){
				$_POST['start_date'] = date('Y-m-d');
			}
			if(trim($_POST['end_date']) == ''){
				unset($_POST['end_date']);
			}
			
			$_validate = array(
				array('title'			, 'require', '广告标题不能为空'),
				array('ad_position_id'	, 'require', '请选择广告位'),
				array('url'				, 'require', '链接地址不能为空'),
				array('image_url'		, 'require', '请上传图片', 1),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			if($data['url'] && strpos($data['url'], 'http://') === false){
				$data['url'] = 'http://'.$data['url'];
			}
			$data['orderid'] = 0;
			$data['create_time'] = time();
			$insert_id = $model->add($data);
			if($insert_id > 0){
				//"保存并继续添加"
				if($_POST['submit_continue']){
					$this->success('添加成功');
				}else{
					$this->success('添加成功', cookie('_currentUrl_'));
				}
			}else{
				$this->error('添加失败');
			}
		}
		
		// 获取广告位列表
		$ad_position_list = D("AdPosition")->order('orderid asc')->select();
		$ad_position_json = json_encode(array_key_list($ad_position_list));
		
		$this->assign('ad_position_list', $ad_position_list);
		$this->assign('ad_position_json', $ad_position_json);
		$this->display('edit');
	}
	
	
	/**
	 * 修改广告
	 */
	public function edit(){
		
		$model = D("Ad");
		
		if($this->isPost()){
			
			$_POST['title'] = $this->_post('title');
			if(trim($_POST['start_date']) == ''){
				$_POST['start_date'] = date('Y-m-d');
			}
			if(trim($_POST['end_date']) == ''){
				$_POST['end_date'] = NULL;
			}
			
			$_validate = array(
				array('title'			, 'require', '广告标题不能为空'),
				array('ad_position_id'	, 'require', '请选择广告位'),
				array('url'				, 'require', '链接地址不能为空'),
				array('image_url'		, 'require', '请上传图片', 1),
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
		
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('id参数为空');
		}
		$ad = $model->find($id);
		if(!$ad){
			$this->error('广告不存在');
		}
		
		// 获取广告位列表
		$ad_position_list = D("AdPosition")->order('orderid asc')->select();
		$ad_position_json = json_encode(array_key_list($ad_position_list));
		
		$this->assign('ad_position_list', $ad_position_list);
		$this->assign('ad_position_json', $ad_position_json);
		$this->assign('_model', $ad);
		$this->display();
	}
	
	
	/**
	 * 删除广告
	 */
	public function delete(){
		$model = D('Ad');
		
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
	 * 上架下架
	 */
	public function toggle(){
		$is_show = $this->_get('is_show');
		
		if($this->isPost()){
			// 批量下架上架
			if($_POST['ids']){
				$this->_toggle_field(D('Ad'), $_POST['ids'], $is_show, 'is_show');
			}
			$this->ajaxReturn('', '更新成功', 1);
		}
	}
	
}
?>