<?php
/**
 +----------------------------------------------------------------------------
 * 后台登录日志 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.0.2 Build 20140211
 +------------------------------------------------------------------------------
 */
class LoginLogAction extends AdminCommonAction{
	
	/**
	 * 查询日志
	 */
	public function index(){
		$model = D("LoginLog");
		
		$status 	= (isset($_GET['status']) && in_array($_GET['status'], array(1,2))) ? $_GET['status'] : '';
		$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
		$end_date 	= isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
		$keyword 	= isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
		
		$map = array();
		if($status){
			$map['status'] = $status;
			$this->assign('status', $status);
		}
		if($start_date && $end_date){
			$map['login_time'] = array('between', array(strtotime($start_date), strtotime($end_date.' 23:59:59')));
			$this->assign('start_date', $start_date);
			$this->assign('end_date', $end_date);
		}
		if($keyword != ''){
			$where['username'] 	= array('like', "%$keyword%");
			$where['ext']		= array('like', "%$keyword%");
			$where['login_ip']	= array('like', "%$keyword%");
			$where['_logic']	= 'or';
			$map['_complex']	= $where;
			$this->assign('keyword', $keyword);
		}
		
		$list = $this->_list($model, $map, 'id', 'desc');
		
		$this->display();
	}
	
}
?>