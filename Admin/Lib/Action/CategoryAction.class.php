<?php
/**
 +----------------------------------------------------------------------------
 * 分类管理 控制器类
 +----------------------------------------------------------------------------
 * @author fanrong33 <fanrong33#qq.com>
 * @version v1.0.6 Build 20140305
 +------------------------------------------------------------------------------
 */
class CategoryAction extends AdminCommonAction{
	
	/**
	 * 查询分类
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function index(){
		$name = $this->getActionName();
		$model = D($name);
		
		$category_list = $model->order('orderid asc,id desc')->select();
		$category_tree = list_to_tree($category_list);
		
        $this->assign('tree', $category_tree);
        
		cookie('_currentUrl_', __SELF__);
		$this->display();
	}
	
	/**
     * 显示分类树，仅支持内部调用
     * @param  array $tree 分类树
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
	    if($this->isPost()){
	        if($_POST['orderid']){
	            // 保存排序
	            $this->_sort(M("Category"), $_POST['orderid']);
	        }
	        $this->ajaxReturn('', '保存排序成功', 1);
	    }
	}

	/**
	 * 添加分类
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			$_POST['name'] 	= trim($this->_post('name'));
			
			$_validate = array(
				array('title'	, 'require'	, '请填写分类名称', 1),
				array('name'	, 'require'	, '请填写英文目录', 1),
//				array('name'	, '', '类别名称已存在', 1, 'unique'),
			);

			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			if($data['pid'] == 0){
				$data['level'] = 1;
			}else{
				$parent_category = $model->find($data['pid']);
				$data['level'] = $parent_category['level'] + 1;
			}
			$data['orderid'] = 0;
			$data['create_time'] = time();
			
			$insert_id = $model->add($data);
			if($insert_id > 0){
				// ”保存并继续添加“
				if($_POST['submit_continue']){
					$this->success('添加成功');
				}else{
					$this->success('添加成功', cookie('_currentUrl_'));
				}
			}else{
				$this->error('添加失败');
			}
		}
		
		// 获取上级分类select options
		$category_list = $model->order('orderid asc,id desc')->select();
		$category_list = array_key_list($category_list, 'id');
		
		// 创建树状 option string
		import('@.ORG.Form.Tree');
		$tree = new tree();
		$tree->init($category_list);
		$str = "<option value=\$id \$selected>\$spacer \$title</option>";
		
		$this->assign('options', $tree->get_tree(0, $str, 0));
		$this->display('edit');
	}
	
	/**
	 * 修改分类
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function edit(){
		$id = $this->_get('id', 'id参数不能为空');
		
		$name = $this->getActionName();
		$model = D($name);
		
		$category = $model->find($id);
		if(!$category){
			$this->error('类别不存在');
		}
		
		if($this->isPost()){
			
			$_POST['title'] = trim($this->_post('title'));
			$_POST['name']	= trim($this->_post('name'));
			
			$_validate = array(
				array('title'	, 'require'	, '请填写分类名称', 1),
				array('name'	, 'require'	, '请填写英文目录', 1),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			if($data['pid'] == 0){
				$data['level'] = 1;
			}else{
				$parent_category = $model->find($data['pid']);
				$data['level'] = $parent_category['level'] + 1;
			}
			$data['update_time'] = time();
			$effect = $model->where(array('id'=>$id))->save($data);
			if($effect){
				$this->success('修改成功');
			}else{
				$this->success('修改失败');
			}
		}
		
		// 获取上级分类select options
		$category_list = $model->order('orderid asc,id desc')->select();
		$category_list = array_key_list($category_list, 'id');
		
		// 创建树状 option string
		import('@.ORG.Form.Tree');
		$tree = new tree();
		$tree->init($category_list);
		$str = "<option value=\$id \$selected>\$spacer \$title</option>";
		
		$this->assign('_model', $category);
		$this->assign('options', $tree->get_tree(0, $str, $category['pid']));
		$this->display();
	}
	
	/**
	 * 删除分类
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function delete(){
		$name = $this->getActionName();
		$model = D($name);
		
		$id = $this->_get('id', '参数id不能为空');
		
		// 判断该分类下是否存在子分类
		if($model->where(array('pid'=>$id))->find()){
			$this->error('请先删除该分类下的子分类');
		}
		
		$effect = $model->delete($id);
		if($effect){
			$this->success('删除成功');
		}
	}

}
?>