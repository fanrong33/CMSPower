<?php
/**
 +----------------------------------------------------------------------------
 * 栏目管理 控制器类
 +----------------------------------------------------------------------------
 * @author fanrong33 <fanrong33#qq.com>
 * @version v1.3.2 Build 20140306
 +------------------------------------------------------------------------------
 */
class ChannelAction extends AdminCommonAction{

	/**
	 * 查询栏目
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function index(){
		$name = $this->getActionName();
		$model = D($name);
		
		// 获取栏目树状列表,关联获取得到栏目所属模型
		$channel_table  = $model->getTableName();
		$module_table	= D("Module")->getTableName();
		$sql = <<<EOF
select a.*,b.name as module_name,b.title as module_title 
from $channel_table a left join $module_table b 
on a.module_id=b.id 
order by orderid asc,id desc
EOF;
		
		$channel_list = $model->query($sql);
		$channel_tree = list_to_tree($channel_list);
		
		$this->assign('tree', $channel_tree);
		
		cookie('_currentUrl_', __SELF__);
		$this->assign('channel_tree', $channel_tree);
		$this->display();
	}
	
	/**
	 * 显示分类树，仅支持内部调用
	 * @param array $tree 分类树
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function tree($tree = null){
		$this->assign('tree', $tree);
		$this->display('tree');
	}
	
	/**
	 * 保存排序
	 * @author fanrong33 <fanrong33#qq.com>
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
	 * 添加栏目
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
		
		$type = $this->_get('type');
		if(!in_array($type, array(1,2,3))){
			$this->error('参数type错误');
		}
		
		if($type == 2){
			$module_id = D('Module')->where(array('name'=>'Page'))->getField('id');
		}
		
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			$_POST['name'] 	= trim($this->_post('name'));
			
			if($type == 1){ // 内部栏目
				$_validate = array(
					array('module_id', 'require', '关联模型不能为空'),
					array('title'	, 'require'	, '栏目名称不能为空'),
					array('name'	, 'require'	, '英文目录不能为空'),
					array('name'	, ''		, '该英文目录名已被使用', 1, 'unique'),
				);
			}elseif($type == 2){ // 单网页
				$_validate = array(
					array('title'	, 'require'	, '栏目名称不能为空'),
					array('name'	, 'require'	, '英文目录不能为空'),
					array('name'	, ''		, '该英文目录名已被使用', 1, 'unique'),
				);
				// 获取单页模型的module_id
				$_POST['module_id'] = $module_id;
			}elseif($type == 3){ // 外部链接
				$_validate = array(
					array('title'	, 'require'	, '栏目名称不能为空'),
					array('name'	, 'require'	, '英文目录不能为空'),
					array('url'		, 'require'	, '链接地址不能为空'),
					array('name'	, ''		, '该英文目录名已被使用', 1, 'unique'),
				);
			}

			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			if($data['pid'] == 0){
				$data['level'] = 1;
			}else{
				$parent_channel = $model->find($data['pid']);
				$data['level'] = $parent_channel['level'] + 1;
			}
			$data['orderid'] = 0;
			$data['setting'] = serialize($data['setting']);
			$data['create_time'] = time();
			
			$insert_id = $model->add($data);
			if($insert_id > 0){
				if($_POST['submit_continue']){
					$this->success('添加成功');
				}else{
					$this->success('添加成功', cookie('_currentUrl_'));
				}
			}else{
				$this->error('添加失败');
			}
		}
		
		$channel_list = $model->where(array('level'=>array('elt', 2)))->order('orderid asc,id desc')->select();
		$channel_list = array_key_list($channel_list, 'id');
		
		// 创建树状 option string
		import('@.ORG.Form.Tree');
		$tree = new tree();
		$tree->init($channel_list);
		$str = "<option value=\$id \$selected>\$spacer \$title</option>";
		
		// 获取分类树状列表
		$category_list = D('Category')->where(array('level'=>array('elt', 2)))->order('orderid asc,id desc')->select();
		$category_list = array_key_list($category_list, 'id');
		
		$category_tree = new tree();
		$category_tree->init($category_list);
		$category_str = "<option value=\$id \$selected>\$spacer \$title</option>";
		
		
		// 获取关联模型列表
		$module_list = D("Module")->order('orderid asc, id desc')->select();
		
		// 添加单网页时，获取Page模型对应模板文件
		if($type==2){
			$html_list = $this->get_module_tpl($module_id);
			$this->assign('html_list', $html_list);
		}
		
		$this->assign('type', $type);
		$this->assign('options', $tree->get_tree(0, $str, 0));
		$this->assign('category_options', $category_tree->get_tree(0, $category_str));
		$this->assign('module_list', $module_list);
		$this->display('edit_'.$type);
	}
	
	
	/**
	 * 修改栏目
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function edit(){
		$id = $this->_get('id', '参数id不能为空');
		
		$name = $this->getActionName();
		$model = D($name);
		
		$channel = $model->find($id);
		if(!$channel){
			$this->error('栏目不存在');
		}
		$channel['setting'] = unserialize($channel['setting']);
		
		if($this->isPost()){
			
			$_POST['name']	= trim($this->_post('name'));
			$_POST['title'] = trim($this->_post('title'));
			
			if($channel['type'] == 1){
				$_validate = array(
					array('module_id', 'require', '关联模型不能为空'),
					array('title'	, 'require'	, '栏目名称不能为空'),
					array('name'	, 'require'	, '英文目录不能为空'),
					array('name'	, ''		, '该英文目录名已被使用', 1, 'unique'),
				);
			}elseif($channel['type'] == 2){
				$_validate = array(
					array('title'	, 'require'	, '栏目名称不能为空'),
					array('name'	, 'require'	, '英文目录不能为空'),
					array('name'	, ''		, '该英文目录名已被使用', 1, 'unique'),
				);
			}elseif($channel['type'] == 3){
				$_validate = array(
					array('title'	, 'require'	, '栏目名称不能为空'),
					array('name'	, 'require'	, '英文目录不能为空'),
					array('url'		, 'require'	, '链接地址不能为空'),
					array('name'	, ''		, '该英文目录名已被使用', 1, 'unique'),
				);
			}
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			if($data['pid'] == 0){
				$data['level'] 	 = 1;
			}else{
				$parent_channel = $model->find($data['pid']);
				$data['level'] 	 = $parent_channel['level'] + 1;
			}
			$data['setting'] 	 = serialize($data['setting']);
			$data['update_time'] = time();
			$effect = $model->where(array('id'=>$id))->save($data);
			if($effect){
				$this->success('修改成功');
			}else{
				$this->success('修改失败');
			}
		}
		
		// 获取栏目树状列表
		$channel_list = $model->where(array('level'=>array('elt', 2)))->order('orderid asc,id desc')->select();
		$channel_list = array_key_list($channel_list, 'id');
		
		import('@.ORG.Form.Tree');
		$tree = new tree();
		$tree->init($channel_list);
		$str = "<option value=\$id \$selected>\$spacer \$title</option>";
		
		// 获取分类树状列表
		$category_list = D('Category')->where(array('level'=>array('elt', 2)))->order('orderid asc,id desc')->select();
		$category_list = array_key_list($category_list, 'id');
		
		$category_tree = new tree();
		$category_tree->init($category_list);
		$category_str = "<option value=\$id \$selected>\$spacer \$title</option>";

		
		// 获取模型列表
		$module_list = D("Module")->order('orderid asc, id desc')->select();
		
		// 获取模型对应模板文件
		if($channel['type'] == 1 || $channel['type'] == 2){
			$html_list = $this->get_module_tpl($channel['module_id']);
		}
		
		$this->assign('options', $tree->get_tree(0, $str, $channel['pid']));
		$this->assign('category_options', $category_tree->get_tree(0, $category_str, $channel['category_pid']));
		$this->assign('module_list', $module_list);
		$this->assign('html_list', $html_list);
		$this->assign('_model', $channel);
		$this->display('edit_'.$channel['type']);
	}
	
	
	/**
	 * 删除栏目
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function delete(){
		$id = $this->_get('id', '参数id不能为空');
		
		$name = $this->getActionName();
		$model = D($name);
		
		// 判断该栏目下级是否存在子栏目
		$cond = array();
		$cond['pid'] = $id;
		if($model->where($cond)->find()){
			$this->error('该栏目下存在子栏目，不允许删除');
		}
		
		$effect = $model->delete($id);
		if($effect){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
	
	/**
	 * ajax获取模型对应前台模板，仅供内部调用
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function ajax_get_module_tpl(){
		// 获取模型对应
		$module_id = $this->_get('module_id');
		$module = D('Module')->find($module_id);
		
		$tpl_path = 'Home/Tpl/';
		$dir_path = $tpl_path.$module['name'];
		
		import('@.ORG.Io.Dir');
		$Dir = new Dir($dir_path, '*.html');
		$html_list = $Dir->toArray();
		
		$tmp_list = array(
			'tpl_channel' => array(),
			'tpl_index'   => array(),
			'tpl_detail'  => array(),
		);
		
		foreach($html_list as $html){
			if(substr($html['filename'], 0, 7) == 'channel'){
				$tmp_list['tpl_channel'][] = $html['filename'];
			}
			if(substr($html['filename'], 0, 5) == 'index'){
				$tmp_list['tpl_index'][]   = $html['filename'];
			}
			if(substr($html['filename'], 0, 6) == 'detail'){
				$tmp_list['tpl_detail'][]  = $html['filename'];
			}
		}
		natsort($tmp_list['tpl_channel']);
		natsort($tmp_list['tpl_index']);
		natsort($tmp_list['tpl_detail']);
		$html_list = $tmp_list;
		unset($tmp_list);
		$this->ajaxReturn($html_list, '获取模型文件夹下的模板文件成功', 1);
	}
	
	/**
	 * 循环遍历前台模板，目前暂不支持多主题模板（default、touch）
	 */
	private function get_module_tpl($module_id){
		$module = D('Module')->find($module_id);
		
		$tpl_path = 'Home/Tpl/';
		$dir_path = $tpl_path.$module['name'];
		
		import('@.ORG.Io.Dir');
		$Dir = new Dir($dir_path, '*.html');
		$html_list = $Dir->toArray();
		
		$tmp_list = array(
			'tpl_channel' => array(),
			'tpl_index'   => array(),
			'tpl_detail'  => array(),
			'tpl_page'    => array(),
		);
		
		foreach($html_list as $html){
			if(substr($html['filename'], 0, 7) == 'channel'){
				$tmp_list['tpl_channel'][] = $html['filename'];
			}
			if(substr($html['filename'], 0, 5) == 'index'){
				$tmp_list['tpl_index'][]   = $html['filename'];
			}
			if(substr($html['filename'], 0, 6) == 'detail'){
				$tmp_list['tpl_detail'][]  = $html['filename'];
			}
			if(substr($html['filename'], 0, 4) == 'page'){
				$tmp_list['tpl_page'][]  = $html['filename'];
			}
		}
		natsort($tmp_list['tpl_channel']);
		natsort($tmp_list['tpl_index']);
		natsort($tmp_list['tpl_detail']);
		natsort($tmp_list['tpl_page']);
		$html_list = $tmp_list;
		unset($tmp_list);
		return $html_list;
	}
	
}

/**
 * TODO
 * 1、后台前端模板不知道哪个栏目有作为导航进行显示
 * 2、Channel依赖Module和Category模型，对开发者未做容错友好提示
 * 3、表单验证还不够完善
 */
 
?>