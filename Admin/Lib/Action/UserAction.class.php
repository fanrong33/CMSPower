<?php
/**
 * 后台用户 控制器类
 * 
 * 后台用户登录的接口为 $_SESSION['admin_id']
 * @author fanrong33
 * @version v1.2.1 Build 20150321
 */
class UserAction extends Action {
	
    public function index(){
    	exit;
    }
    
    /**
     * 登录
     */
    public function login(){
    	//dump(GS('base.site_name'));

    	if($this->isPost()){
    		$username = trim($_POST['username']);
    		$password = $_POST['password'];
    		$verify	  = $_POST['verify'];

            $need_to_verify = GS('base.need_to_verify', false); // 默认不验证 //TODO 系统配置加入base.need_to_verify

    		if($need_to_verify && $verify == ''){
                $this->error('验证码不能为空');
    		}

    		if($username=='' || $password==''){
    			
    			if($username != ''){
	    			try {
		    			// 加入登录日志
			    		$LoginLog = D("LoginLog");
		    			$LoginLog->addLog($username, '密码为空', 2);
					}catch( Exception $e ) {
					}
    			}
    			$this->error('用户名和密码不能为空');
    		}
    		if($need_to_verify && md5($verify) != $_SESSION['verify']){
    			$this->error('验证码错误');
    		}
    		
    		$admin = D("Admin")->where(array('username'=>$username))->find();
    		if($admin && encrypt_pwd($password) == $admin['password']){
    			if($admin['id'] != 1 && $admin['is_lock'] == '1'){
	    			try {
		    			// 加入登录日志
			    		$LoginLog = D("LoginLog");
		    			$LoginLog->addLog($username, '帐号被禁用', 2);
					}catch( Exception $e ) {
					}
    				$this->error('对不起，该管理员已被禁用，请联系管理员');
    			}
    		}else{
    			try {
	    			// 加入登录日志
		    		$LoginLog = D("LoginLog");
	    			$LoginLog->addLog($username, '帐号密码错误', 2);
				}catch( Exception $e ) {
				}
    			$this->error('用户名或密码错误');
    		}
    		
    		// 登录成功
    		$_SESSION['admin_id'] 			= $admin['id'];
    		$_SESSION['last_login_ip'] 		= $admin['last_login_ip'];
    		$_SESSION['last_login_time'] 	= $admin['last_login_time'];
    		
    		// 更新状态“上次登录IP”、“上次登录时间”和“登录次数”
    		$data = array();
    		$data['last_login_ip'] 		= get_client_ip();
    		$data['last_login_time']	= time();
    		$data['login_times'] 		= array('exp', 'login_times+1');
    		D("Admin")->where(array('id'=>$admin['id']))->save($data);
    		
    		try {
    			// 加入登录日志
	    		$LoginLog = D("LoginLog");
    			$LoginLog->addLog($username);
			}catch( Exception $e ) {
			}
    		
    		// 记住用户名
    		cookie('remember', $username, array('expire'=>3600*24*30));
    		
//			redirect(_PHP_FILE_);
			//TODO 改进人性化
			$this->ajaxReturn(U('AboutUs/index'), '登录成功', 1);
//			$this->success('登录成功');
    	}
    	$this->display();
    }
    
    
    /**
     * 退出
     */
    public function logout(){
    	unset($_SESSION['admin_id']);
    	redirect(_PHP_FILE_);
    }
    
}