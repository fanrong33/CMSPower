<?php
/**
 +----------------------------------------------------------------------------
 * 登录日志 模型类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.0 Build 20130710
 +------------------------------------------------------------------------------
 */
class LoginLogModel extends Model{
	
	/**
	 * 添加登录日志
	 * @param string $username 后台登录用户名
	 * @param string $ext 其他说明
	 * @param string $status '0'-登录失败，'1'-登录成功
	 */
	public function addLog($username='', $ext='', $status=1){
		
		$data = array();
		$data['username'] 	= $username;
		$data['login_time'] = time();
		$data['login_ip'] 	= get_client_ip();
		$data['status'] 	= $status.'';
		$data['ext'] 		= $ext;
		$data['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$effect = $this->add($data);
		return $effect;
	}
	
}	
?>