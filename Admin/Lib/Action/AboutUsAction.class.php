<?php
/**
 +----------------------------------------------------------------------------
 * 关于我们 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33 <fanrong33#qq.com>
 * @version v1.0.5 Build 20140305
 +------------------------------------------------------------------------------
 */
class AboutUsAction extends AdminCommonAction{

	/**
	 * 关于我们
	 */
	public function index(){
		
		// 获取管理员信息
		$admin = D("AdminView")->find($this->_admin_id);
		
		// 计算 当前数据库尺寸
		$sql = 'SHOW TABLE STATUS FROM '.C('DB_NAME'); 
		
		$model = new Model();
		$table_status_list = $model->query($sql);
		$db_size = 0;
		foreach($table_status_list as $rs){
			$db_size += $rs['Data_length'];
		}
		
		$this->assign('admin', $admin);
		$this->assign('db_size', byte_format($db_size, 2));
		$this->display();
	}
	
}
?>