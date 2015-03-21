<?php
/**
 +----------------------------------------------------------------------------
 * Memcache监控 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.1.1 Build 20140225
 +------------------------------------------------------------------------------
 */
class MemcacheStatAction extends AdminCommonAction{
	
	/**
	 * 查询Memcache状态
	 */
	public function index(){
		if(isset($_GET['op']) && $_GET['op']=='flush'){
			$mem = new Memcache;
			$mem->connect(C('MEMCACHE_HOST'), C('MEMCACHE_PORT'));
			$result = $mem->flush();
		}
		
		// 获取memcache的状态
		$stats = $this->getMemStats();
		
		// 计算memcache使用比率
		$free_bytes = byte_format(($stats['limit_maxbytes'] - $stats['bytes']), 1);
		$used_bytes = byte_format($stats['bytes'], 1);
		
		$free_percentage = ($stats['limit_maxbytes'] - $stats['bytes'])/$stats['limit_maxbytes']*100;
		$free_percentage = number_format($free_percentage, 1, '.', ',');
		$used_percentage = $stats['bytes']/$stats['limit_maxbytes']*100;
		$used_percentage = number_format($used_percentage, 1, '.', ',');
		
		// 计算命中的百分比和未命中的百分比
		$hits_percentage 	= $stats['get_hits']/($stats['get_hits']+$stats['get_misses'])*100;
		$hits_percentage 	= number_format($hits_percentage, 1, '.', ',');
		
		$misses_percentage 	= $stats['get_misses']/($stats['get_hits']+$stats['get_misses'])*100;
		$misses_percentage 	= number_format($misses_percentage, 1, '.', ',');
		
		$this->assign('_stats', $stats);
		
		$this->assign('free_bytes', $free_bytes);
		$this->assign('used_bytes', $used_bytes);
		$this->assign('free_percentage', $free_percentage);
		$this->assign('used_percentage', $used_percentage);
		
		$this->assign('hits_percentage', $hits_percentage);
		$this->assign('misses_percentage', $misses_percentage);
		
		$this->display();
	}
	
	
	/**
	 * 获取memcache的实时状态
	 */
	private function getMemStats(){
		$mem = new Memcache;
		$mem->connect(C('MEMCACHE_HOST'), C('MEMCACHE_PORT'));

		$stats = $mem->getStats();
		
		return $stats;	
	}
	
}
?>