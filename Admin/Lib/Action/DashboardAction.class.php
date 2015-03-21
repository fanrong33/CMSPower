<?php
/**
 +----------------------------------------------------------------------------
 * 面板 控制器类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.4 Build 20130718
 +------------------------------------------------------------------------------
 */
class DashboardAction extends AdminCommonAction{
	
	public function index(){
		
		// 总收入：计算今天状态为完成的订单的价格
		$today_start	= strtotime(date('Y-m-d'));
		$today_end 		= strtotime(date('Y-m-d 23:59:59'));
//		$cond = array();
//		$cond['create_time'] 	= array('between', array($today_start, $today_end));
//		$cond['status'] 		= '4';
//		$total_income = D("Order")->where($cond)->sum('total');
		$total_income = 520;
		
		// 等待确认的订单
//		$cond = array();
//		$cond['create_time'] 	= array('between', array($today_start, $today_end));
//		$cond['status'] 		= '1';
//		$todo_count = D("Order")->where($cond)->count();
		$todo_count = 5;
		
		// 新增订单：今日创建的订单，不仅仅是完成的，包括所有的
//		$cond = array();
//		$cond['create_time'] 	= array('between', array($today_start, $today_end));
//		$new_count = D("Order")->where($cond)->count();
		$new_count = 32;
		
		// 完成订单：今日完成的订单，收到款的
//		$cond = array();
//		$cond['create_time'] 	= array('between', array($today_start, $today_end));
//		$cond['status'] 		= '4';
//		$complete_count = D("Order")->where($cond)->count();
		$complete_count = 25;
		
		// 新增注册用户
		$cond = array();
//		$cond['create_time'] = array('between', array($today_start, $today_end));
		$member_count = D("Member")->where($cond)->count();
		

		// Highcharts 最近30天的收入area
		$recent_30day = mktime(0, 0, 0, date("m"), date("d")-29,   date("Y"));
		$cond = array();
//		$cond['create_time'] = array('between', array($recent_30day, $today_end));
//		$cond['status'] = '4';
//		$income_list = D("Order")->where($cond)->field('dateline, sum(total) as total')->group('dateline')->select();
//		$income_list = array_key_list($income_list, 'dateline');
//		$tmp_list = array();
//		for($i=0; $i<30; $i++){
//			$key = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-29+$i,   date("Y")));
//			$tmp_list[$key] = $income_list[$key]['total'] ? $income_list[$key]['total'] : '0';
//		}
//		$income_list = $tmp_list;
//		unset($tmp_list);
//		$income = join(',', $income_list);

		// 模拟数据： /////////////////////////////////////////////////////
		for($i=0; $i<30; $i++){
			if(in_array($i, explode(',', '2,8,9,15,16,22,23,29,30'))){
				$income[] = 0;
			}else{
				$income[] = rand(0, 1000);
			}
		}
		$income = join(',', $income);
		//////////////////////////////////////////////////////////////////

		$recent_30day_date_utc = date('Y,n,d', mktime(0, 0, 0, date('m')-1, date('d')-29, date('Y')));
		
		$this->assign('total_income', $total_income);
		
		$this->assign('todo_count', $todo_count);
		$this->assign('new_count', $new_count);
		$this->assign('complete_count', $complete_count);
		
		$this->assign('member_count', $member_count);
		
		$this->assign('income', $income);
		$this->assign('recent_30day_date_utc', $recent_30day_date_utc);
		
		$this->order_count();
		$this->member_count();
		
		$this->display();
	}
	
	/**
	 * 最近30天的订单数 column
	 */
	public function order_count(){
//		$recent_30day = mktime(0, 0, 0, date("m"), date("d")-(30-1),   date("Y"));
//		$today_end = strtotime(date('Y-m-d 23:59:59'));
//		$cond['create_time'] = array('between', array($recent_30day, $today_end));
//		$cond['status'] = '4';
//		$order_count_list = D("Order")->where($cond)->field('dateline, count(id) as orders')->group('dateline')->select();
//		$order_count_list = array_key_list($order_count_list, 'dateline');
//
//		$tmp_list = array();
//		for($i=0; $i<30; $i++){
//			$key = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-(30-1)+$i,   date("Y")));
//			$tmp_list[$key] = (isset($order_count_list[$key]['orders']) && $order_count_list[$key]['orders']) ? $order_count_list[$key]['orders'] : '0';
//		}
//		$order_count_list = $tmp_list;
//		$order_count_list = array_values($order_count_list);
//		unset($tmp_list);
		
		// 模拟数据： /////////////////////////////////////////////////////
		for($i=0; $i<30; $i++){
			if(in_array($i, explode(',', '2,8,9,15,16,22,23,29,30'))){
				$order_count_list[] = 0;
			}else{
				$order_count_list[] = rand(0, 50);
			}
		}
		//////////////////////////////////////////////////////////////////
		
		$max = 0;
		$order_avg = array_sum($order_count_list)/count($order_count_list);
		foreach($order_count_list as $order_count){
			if($order_count > $max){
				$max = $order_count;
			}
		}
		foreach($order_count_list as $key=>$order_count){
			$order_count_list[$key] = array($order_count, ceil($order_count/$max*100));
		}
		$this->assign('order_count_list', $order_count_list);
		$this->assign('order_avg', $order_avg);
	}
	
	
	/**
	 * 最近30天的注册用户数 column
	 */
	public function member_count(){
//		$recent_30day = mktime(0, 0, 0, date("m"), date("d")-(30-1), date("Y"));
//		$today_end = strtotime(date('Y-m-d 23:59:59'));
//		$cond['create_time'] = array('between', array($recent_30day, $today_end));
//		$member_count_list = D("Member")->where($cond)->field('dateline, count(id) as members')->group('dateline')->select();
//		$member_count_list = array_key_list($member_count_list, 'dateline');

//		$tmp_list = array();
//		for($i=0; $i<30; $i++){
//			$key = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-(30-1)+$i,   date("Y")));
//			$tmp_list[$key] = (isset($member_count_list[$key]['members']) && $member_count_list[$key]['members']) ? $member_count_list[$key]['members'] : '0';
//		}
//		$member_count_list = $tmp_list;
//		$member_count_list = array_values($member_count_list);
//		unset($tmp_list);

		// 模拟数据： /////////////////////////////////////////////////////
		for($i=0; $i<30; $i++){
			if(in_array($i, explode(',', '2,8,9,15,16,22,23,29,30'))){
				$member_count_list[] = 0;
			}else{
				$member_count_list[] = rand(0, 50);
			}
		}
		//////////////////////////////////////////////////////////////////
		
		$max = 0;
		$member_avg = array_sum($member_count_list)/count($member_count_list);
		foreach($member_count_list as $order_count){
			if($order_count > $max){
				$max = $order_count;
			}
		}
		foreach($member_count_list as $key=>$order_count){
			$member_count_list[$key] = array($order_count, ceil($order_count/$max*100));
		}
		$this->assign('member_count_list', $member_count_list);
		$this->assign('member_avg', $member_avg);
	}
	
}
?>