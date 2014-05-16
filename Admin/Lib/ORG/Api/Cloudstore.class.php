<?php
/**
 +----------------------------------------------------------------------------
 * PHP Library for cloudstore
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.3 Build 20131208
 +----------------------------------------------------------------------------
 */
class cloudstorePHP
{
	function __construct($appid, $appkey, $access_token=NULL){
		$this->appid=$appid;
		$this->appkey=$appkey;
		$this->access_token=$access_token;
	}

	function login_url($callback_url){
		$params=array(
			'client_id'=>$this->appid,
			'redirect_uri'=>$callback_url,
			'response_type'=>'code',
			'scope'=>''
		);
		return 'https://graph.qq.com/oauth2.0/authorize?'.http_build_query($params);
	}

	function access_token($callback_url, $code){
		$params=array(
			'grant_type'=>'authorization_code',
			'client_id'=>$this->appid,
			'client_secret'=>$this->appkey,
			'code'=>$code,
			'state'=>'',
			'redirect_uri'=>$callback_url
		);
		$url='https://graph.qq.com/oauth2.0/token?'.http_build_query($params);
		$result_str=$this->http($url);
		$json_r=array();
		if($result_str!='')parse_str($result_str, $json_r);
		return $json_r;
	}

	/**
	function access_token_refresh($refresh_token){
	}
	**/

	function get_openid(){
		$params=array(
			'access_token'=>$this->access_token
		);
		$url='https://graph.qq.com/oauth2.0/me?'.http_build_query($params);
		$result_str=$this->http($url);
		$json_r=array();
		if($result_str!=''){
			preg_match('/callback\(\s+(.*?)\s+\)/i', $result_str, $result_a);
			$json_r=json_decode($result_a[1], true);
		}
		return $json_r;
	}
	
	/**
	 * 获取所有APP后台应用列表
	 * @reutrn array $json
	 */
	function get_app_list(){
		$params=array(
			'oauth_consumer_key'=>$this->appid,
			'format'=>'json'
		);
		$url='http://cmspower2.com/app.php/cloud_store/get_app_list';
		return $this->api($url, $params);
	}
	
	/**
	 * 通过app_id获取APP后台应用
	 * 
	 * @request method GET
	 * @param int $app_id 应用ID
	 */
	function get_app($app_id){
		$params=array(
			'app_id' => $app_id,
			'oauth_consumer_key'=>$this->appid,
			'format'=>'json'
		);
		$url='http://cmspower2.com/app.php/cloud_store/get_app';
		return $this->api($url, $params);
	}
	
	/**
	 * 获取所有内容模块列表
	 */
	function get_module_list(){
		$params=array(
			'oauth_consumer_key'=>$this->appid,
			'format'=>'json'
		);
		$url='http://cmspower2.com/app.php/cloud_store/get_module_list';
		return $this->api($url, $params);
	}
	
	/**
	 * 通过module_id获取内容模块
	 * 
	 * @request method GET
	 * @param int $module_id 内容模块ID
	 */
	function get_module($module_id){
		$params=array(
			'module_id' => $module_id,
			'oauth_consumer_key'=>$this->appid,
			'format'=>'json'
		);
		$url='http://cmspower2.com/app.php/cloud_store/get_module';
		return $this->api($url, $params);
	}
	
	/**
	 * 获取所有挂件列表
	 * 
	 * @request method GET
	 */
	function get_widget_list(){
		$params=array(
			'oauth_consumer_key'=>$this->appid,
			'format'=>'json'
		);
		$url='http://cmspower2.com/app.php/cloud_store/get_widget_list';
		return $this->api($url, $params);
	}
	
	/**
	 * 通过widget_id获取挂件
	 * 
	 * @request method GET
	 * @param int $widget_id 挂件ID
	 */
	function get_widget($widget_id){
		$params=array(
			'widget_id' => $widget_id,
			'oauth_consumer_key'=>$this->appid,
			'format'=>'json'
		);
		$url='http://cmspower2.com/app.php/cloud_store/get_widget';
		return $this->api($url, $params);
	}
	
	
	/**
	 * 获取所有采集节点列表
	 */
	function get_collector_list(){
		$params=array(
			'oauth_consumer_key'=>$this->appid,
			'format'=>'json'
		);
		$url='http://cmspower2.com/app.php/cloud_store/get_collector_list';
		return $this->api($url, $params);
	}
	
	/**
	 * 通过collector_id获取采集节点
	 * 
	 * @request method GET
	 * @param int $collector_id 采集节点ID
	 */
	function get_collector($collector_id){
		$params=array(
			'collector_id' => $collector_id,
			'oauth_consumer_key'=>$this->appid,
			'format'=>'json'
		);
		$url='http://cmspower2.com/app.php/cloud_store/get_collector';
		return $this->api($url, $params);
	}
	
	
	
	function api($url, $params, $method='GET'){
		$params['access_token']=$this->access_token;
		if($method=='GET'){
			$result_str=$this->http($url.'?'.http_build_query($params));
		}else{
			$result_str=$this->http($url, http_build_query($params), 'POST');
		}
		$result=array();
		if($result_str!='')$result=json_decode($result_str, true);
		return $result;
	}

	function http($url, $postfields='', $method='GET', $headers=array()){
		$ci=curl_init();
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		if($method=='POST'){
			curl_setopt($ci, CURLOPT_POST, TRUE);
			if($postfields!='')curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
		}
		$headers[]="User-Agent: cloudstorePHP(fanrong33.com)";
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response=curl_exec($ci);
		curl_close($ci);
		return $response;
	}
}