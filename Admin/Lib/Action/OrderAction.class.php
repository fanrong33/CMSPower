<?php
/**
 +----------------------------------------------------------------------------
 * 订单管理 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.0.2 Build 20131125
 +------------------------------------------------------------------------------
 */
class OrderAction extends AdminCommonAction{
	
	/**
	 * 查询订单
	 */
	public function index(){
		
		$status 	= $_GET['status'] ? intval($_GET['status']) : '';
		$keyword 	= $_GET['keyword'] ? trim($_GET['keyword']) : '';
		$start_date = $_GET['start_date'] ? $_GET['start_date'] : date('Y-m-01');
		$end_date 	= $_GET['end_date'] ? $_GET['end_date'] : date('Y-m-d');
		
		$model = D("Order");
		
		$map = array();
		if($status){
			$map['status'] = $status;
			$this->assign('status', $status);
		}
		if($start_date && $end_date){
			$map['create_time'] = array('between', array(strtotime($start_date), strtotime($end_date.' 23:59:59')));
		}
		if($keyword != ''){
			$where['number']  = array('like', "%$keyword%");
			$where['name']  	= array('like',"%$keyword%");
			$where['_logic'] = 'or';
			$map['_complex'] = $where;
			$this->assign('keyword', $keyword);
		}
		$this->_list($model, $map, 'id', 'desc');
		
		$this->display();
	}
	
	/**
	 * 查看订单
	 */
	public function detail(){
		$model = D("Order");
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('id参数为空');
		}
		$order = $model->find($id);
		if(!$order){
			$this->error('该订单不存在');	
		}
		
		$status = '';
		switch($order['status']){
			case '1':
				$status = '未付款';
				break;
			case '2':
				$status = '<span style="color:green;">已付款</span>';
				break;
			case '3':
				$status = '<span style="color:red;">已取消</span>';
				break;
		}
		
		$this->assign('_model', $order);
		$this->assign('status', $status);
		$this->display();
	}
	
	
	/**
	 * 删除订单
	 */
	public function delete(){
		
		$model = D("Order");
		
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
	 * 确认订单
	 */
	public function confirm(){
		$model = D("Order");
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('id参数为空');
		}
		$order = $model->find($id);
		if(!$order){
			$this->error('该订单不存在');	
		}
		
		if($order['status'] != '1'){
			$this->error('订单已付款或已取消');	
		}
		
		// 订单状态，1-未付款，2-已付款，3-已取消
		$data = array();
		$data['status'] = '2';
		$effect = $model->where(array('id'=>$id))->save($data);
		if($effect){
			$this->success('订单确认成功');
		}else{
			$this->error('订单确认失败');
		}
	}
	
	
	/**
	 * 取消订单
	 */
	public function cancel(){
		$model = D("Order");
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('id参数为空');
		}
		$order = $model->find($id);
		if(!$order){
			$this->error('该订单不存在');	
		}
		
		if($order['status'] != '1'){
			$this->error('订单已付款或已取消');	
		}
		
		// 订单状态，1-未付款，2-已付款，3-已取消
		$data = array();
		$data['status'] = '3';
		$effect = $model->where(array('id'=>$id))->save($data);
		if($effect){
			$this->success('订单取消成功');
		}else{
			$this->error('订单取消失败');
		}
	}
}
?>