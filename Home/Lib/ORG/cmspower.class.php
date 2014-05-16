<?php
/**
 +----------------------------------------------------------------------------
 * CMSPOWER SDK 开放接口类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.2.7 Build 20130913
 +------------------------------------------------------------------------------
 */

/**
 * 栏目管理器
 */
class ChannelManager {
	
	public function __construct(){
		// dump('construct');
	}
	
	/**
     * 取得缓存类实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance() {
       $param = func_get_args();
        return get_instance_of(__CLASS__,'',$param);
    }
    
    /**
     * 获取得到栏目列表
     * 
     * @return array $list 
     */
	public function getChannelList(){
		
		$cache_key = 'CACHE_CHANNEL_LIST'.$_SERVER['SERVER_NAME'];
		$list = S(md5($cache_key));
		
		if(!$list){
			$channel_table  = D("Channel")->getTableName();
			$module_table	= D("Module")->getTableName();
			$sql = <<<EOF
select a.*,b.name as module_name,b.title as module_title 
from $channel_table a left join $module_table b 
on a.module_id=b.id 
order by orderid asc 
EOF;
			
			$list = D("Channel")->query($sql);
			if($list){
				$tmp_list = array();
				foreach($list as $rs){
					$rs['setting'] = unserialize($rs['setting']);
					$tmp_list[] = $rs;
				}
				$list = $tmp_list;
			}
			S(md5($cache_key), $list, 3600);
		}
		return $list;
	}
	
	
	/**
	 * 根据栏目英文目录获取栏目
	 * 
	 * @param string $name 		栏目英文目录
	 * @return mixed $channel
	 */
	public function getChannelByName($name){
		$channel = D('Channel')->where(array('name'=>$name))->find();
		
		if($channel){
		 	if($channel['type'] == '1'){
		 		$module = cache_get('Module', $channel['module_id']);
		 		$channel['module_name']	= $module['name'];
		 		$channel['module_title']= $module['title'];
		 	}elseif($channel['type'] == '2'){
		 		$channel['module_name']	= 'Page';
		 		$channel['module_title']= '单网页模型';
		 	}elseif($channel['type'] == '3'){
		 		
		 	}
		 	$channel['setting'] = unserialize($channel['setting']);
		}
	 	return $channel;
	}
	
}


/**
 * 类别管理器
 *  仅支持2级
 */
class CategoryManager {
	
	public function __construct(){
		// dump('construct');
	}
	
	/**
     * 取得缓存类实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance() {
       $param = func_get_args();
        return get_instance_of(__CLASS__,'',$param);
    }
    
	/**
	 * 根据name获取子类别列表
	 * 
	 * @param string $name 		类别名称
	 * @return mixed $list
	 */
	public function getCategoryListByName($name){
		
		$cache_key = 'CACHE_CATEGORY_LIST'.$_SERVER['SERVER_NAME'];
		$category_list = S(md5($cache_key));
		
		if(!$category_list){
			$category_list = D("Category")->order('orderid asc,id desc')->select();
			S(md5($cache_key), $category_list, 3600);
		}
		
	    $category_map = list_to_tree_key($category_list, 'name');
	    $result = $category_map[$name];
		
		return $result['_child'];
	}
	
}


/**
 * 文章管理器
 */
class ArticleManager {
	
	public function __construct(){
		// dump('construct');
	}
	
	/**
     * 取得文章管理器实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance() {
       $param = func_get_args();
        return get_instance_of(__CLASS__,'',$param);
    }
    
    /**
     * 获取栏目下的文章列表
     * 
     * @param integer $channel_id	指定栏目ID
     * @param integer $number 		返回的文章数量，默认10条
     * @param string  $order_by		排序方式，id-按添加时间降序，pv-按点击量降序
     * 
     * @return array $list
     */
	public function getListByChannelId($channel_id, $number=10, $order_by='id'){
		$cache_key = 'CACHE_ARTICLE_LIST_CHANNEL_ID_'.$channel_id.'_'.$number.'_'.$order_by.$_SERVER['SERVER_NAME'];
		$article_list = S(md5($cache_key));
		
		if(!$article_list){
			
			$cond = array();
			$cond['channel_id'] = $channel_id;
			$cond['is_show'] = '1';
			$article_list = D("Article")->where($cond)->order($order_by.' desc')->limit($number)->select();
			
			if($article_list){
				$channel = cache_get('Channel', $channel_id);
				$tmp_list = array();
				foreach($article_list as $rs){
					// 根据栏目英文名组装文章url
					// http://zhimayou.com/index.php/article/index/ch/travel_news/id/3.html
					$rs['url'] = U('Article/show', array('ch'=>$channel['name'], 'id'=>$rs['id']));
					$tmp_list[] = $rs;
				}
				$article_list = $tmp_list;
			}
			
			S(md5($cache_key), $article_list, 3600);
		}
		return $article_list;
	}
	
}


/**
 * 宝贝管理器
 */
class ItemManager {
	
	public function __construct(){
		// dump('construct');
	}
	
	/**
     * 取得宝贝管理器实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance() {
       $param = func_get_args();
        return get_instance_of(__CLASS__,'',$param);
    }
    
    /**
     * 获取栏目下的宝贝列表
     * @param integer $channel_id	指定栏目ID
     * @param integer $number 		返回的宝贝数量
     * @return array $list 
     */
	public function getListByChannelId($channel_id, $number){
		$domain = get_domain();
		$key = $domain.'CACHE_ITEM_LIST_CHANNEL_ID_'.$channel_id.'_'.$number;
		$list = S(md5($key));
		if(!$list){
			
			$cond = array();
			$cond['channel_id'] = $channel_id;
			$list = D("Item")->where($cond)->order('id desc')->limit($number)->select();
			
			S(md5($key), $list, 3600);
		}
		return $list;
	}
	
}


/**
 * 广告位管理器
 */
class AdPositionManager {
	
	public function __construct(){
		// dump('construct');
	}
	
	/**
     * 取得广告位类实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance() {
       $param = func_get_args();
        return get_instance_of(__CLASS__,'',$param);
    }
    
	
	/**
	 * 根据广告位name英文目录获取到广告位
	 * @param string $name 		广告位英文名
	 * @return array $
	 */
	public function getAdPositionByName($name){
		
		$cache_key = 'CACHE_AD_POSITION_NAME_'.$name.$_SERVER['SERVER_NAME'];
		$ad_position = S(md5($cache_key));
		
		if(!$ad_position){
			$ad_position = D('AdPosition')->where(array('name'=>$name))->find();
			S(md5($cache_key), $ad_position, 3600);
		}
		
	 	return $ad_position;
	}
	
}



/**
 * 广告管理器
 */
class AdManager {
	
	public function __construct(){
		// dump('construct');
	}
	
	/**
     * 取得广告类实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance() {
       $param = func_get_args();
        return get_instance_of(__CLASS__,'',$param);
    }
    
	
	/**
	 * 根据栏目name英文目录获取栏目
	 * @param string $ch 		栏目英文目录
	 * @return array $channel
	 */
	public function getList($ad_position_id){
		
		$cond['ad_position_id'] = $ad_position_id;
		$cond['start_date'] 	= array('elt', date('Y-m-d'));
		$cond['_string'] 		= "end_date is NULL OR end_date >= '".date('Y-m-d')."'";
		$cond['is_show'] 		= '1';
		$list = D("Ad")->where($cond)->order('orderid asc,id desc')->select();
		
	 	return $list;
	}
	
}


/**
 * 链接管理器
 */
class LinkManager {
	
	public function __construct(){
		// dump('construct');
	}
	
	/**
     * 取得链接类实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance() {
       $param = func_get_args();
        return get_instance_of(__CLASS__,'',$param);
    }
    
	
	/**
	 * 获取显示的广告列表
	 * @return array 
	 */
	public function getLinkList(){
		
		$cache_key = 'CACHE_LINK_LIST'.$_SERVER['SERVER_NAME'];
		$link_list = S(md5($cache_key));
		
		if(!$link_list){
			$cond = array();
			$cond['is_show'] = '1';
			$link_list = D("Link")->where($cond)->select();
			S(md5($cache_key), $link_list, 3600);
		}
		
	 	return $link_list;
	}
	
}



/**
 * uri 生成管理器
 */
class URIManager {
	
	public function __construct(){
		// dump('URIManager construct');
	}
	
	/**
     * 取得缓存类实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance() {
       $param = func_get_args();
        return get_instance_of(__CLASS__,'',$param);
    }
    
    /**
     * 返回栏目uri对象数组
     */
    public function channelURI($channel_array){
    	$url_list = array();

		if(isset($channel_array)){
			foreach($channel_array as $rs){
				
				// 业务规则：
				// 根据栏目类型属性，得到链接对象(只能处理1级)
				// 1.栏目
				//		旅游资讯  index.php/module/index/ch/trip
				//						   关联模型       英文目录
				// 2.单网页
				//		关于我们  index.php/page/index/ch/aboutus
				//						   关联模型       英文目录
				// 3.外部链接
				
				$link = array();
				$link['title'] = $rs['title'];
				if($rs['type'] == '1'){
					$link['url'] = U(strtolower($rs['module_name']).'/index', array('ch'=>$rs['name']));
				}elseif($rs['type'] == '2'){
					$link['url'] = U('page/index', array('ch'=>$rs['name']));
				}else{
					$link['url'] = $rs['url'];
					$link['target'] = '_blank';
				}
				
				$url_list[] = $link;
			}
		}
		return $url_list;
    }
    
    
}


/**
 * 推荐位管理器
 */
class PositionManager {
	
	public function __construct(){
		// dump('construct');
	}
	
	/**
     * 取得推荐位类实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance() {
       $param = func_get_args();
        return get_instance_of(__CLASS__,'',$param);
    }
    
	
	/**
	 * 根据推荐位名称获取推荐数据列表
	 * @param string $position_name		推荐位名称
	 * @return array 
	 */
	public function getDataList($position_name){
		
		$cache_key = 'CACHE_POSITION_DATA_LIST_'.$position_name.$_SERVER['SERVER_NAME'];
		$data_list = S(md5($cache_key));
		
		if(!$data_list){
			
			$position = M('Position')->where(array('name'=>$position_name))->find();
			
			$cond = array();
	    	$cond['position_id'] = $position['id'];
	    	$data_list = M('PositionData')->where($cond)->limit($position['max_number'])->order('orderid asc,id desc')->select();
	    	if($data_list){
		    	$tmp_list = array();
	    		foreach($data_list as $rs){
	    			$rs['data'] = unserialize($rs['data']);
	    			$tmp_list[] = $rs;
	    		}
	    		$data_list = $tmp_list;
	    		unset($tmp_list);
	    	}
			
			S(md5($cache_key), $data_list, 3600);
		}
		
	 	return $data_list;
	}
	
}


?>