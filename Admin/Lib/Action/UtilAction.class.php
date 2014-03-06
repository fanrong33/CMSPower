<?php
/**
 +----------------------------------------------------------------------------
 * CMSPower Util工具 控制器类
 +----------------------------------------------------------------------------
 * @category 后台控制器（CMSPower内置）
 * @author fanrong33 <fanrong33#qq.com>
 * @version v1.2.6 Build 20140306
 +------------------------------------------------------------------------------
 */
class UtilAction extends Action{
	
	/**
	 * 验证码
	 */
	public function image_verify(){
		import('@.ORG.Util.Image');
		
		Image::buildImageVerify(4, 1, 'png', 50, 30);
	}
	
	/**
	 * 显示上传附件对话框
	 */
	public function paglet_upload(){
		$field	= $_GET['field'];
		$dir 	= $_GET['dir']; // 加密的
		$multi 	= $_GET['multi']=='true' ?  true : false;
		$mode	= $_GET['mode'];
		$exts 	= $_GET['exts']; // 加密的
		
		$exts = decode($exts, C('AUTH_CODE'));
		
		$upload_limit = $multi ? 20 : 1;
		
		$ext_list = explode(',', $exts);
		foreach($ext_list as $key => $rs){
			$ext_list[$key] = '*.'.$rs;
		}
		$file_type_exts = join(';', $ext_list);
		
		// 获取图库
		import('@.ORG.Io.Dir');
		$Dir = new Dir(__ROOT__.'uploads/');
		$file_list = $Dir->toArray();
		
		$this->assign('field'		, $field);
		$this->assign('dir'			, $dir);
		$this->assign('multi'		, $multi);
		$this->assign('mode'		, $mode);
		$this->assign('upload_limit', $upload_limit);
		$this->assign('exts'		, $exts);
		$this->assign('file_type_exts', $file_type_exts);
		
		$this->assign('file_list'	, $file_list);
		$this->display();
	}
	
	/**
	 * 图库ajax get页面结构
	 */
	public function ajax_dir(){
		$dir_path = $_GET['dirpath'];
		
		// 获取图库
		import('@.ORG.Io.Dir');
		$path = 'uploads/'.$dir_path;
		if(substr($path, -1, 1) != '/'){
			$path .= '/';
		}
		$dir = new Dir(__ROOT__.$path);
		$file_list = $dir->toArray();
		
		
		$db_file_list = D("File")->where(array('save_path'=>$path))->order('id desc')->select();
		if($db_file_list){
			// 存在数据库关联图片
			$file_list = $db_file_list;
		}else{
			// 不存在数据库关联图片，去掉图片类型
			$tmp_list = array();
			foreach($file_list as $rs){
				if($rs['type'] == 'dir'){
					$tmp_list[] = $rs;
				}
			}
			$file_list = $tmp_list;
			unset($tmp_list);
		}
		
		
		$this->assign('dir_path', $dir_path);
		$this->assign('file_list', $file_list);
		$content = $this->fetch();
		$this->ajaxReturn($content, 'success', 1);
	}
	
	
	/**
	 * 上传文件
	 * 保留原始版本
	 */
	public function uploadify(){
		$session_name = session_name();
		if (isset($_POST[$session_name])) {
			// using sessions with uploadify
		    session_id($_POST[$session_name]);
		    session_start();
		}
		
		// 文件上传路径
		$dir = trim($_POST['dir']);
		$dir = decode($dir, C('AUTH_CODE'));
		
		if(!empty($_FILES)){
			
			import('@.ORG.Net.UploadFile');
			$upload = new UploadFile();
	    	// 设置上传文件大小
	    	$upload->maxSize 		= 1024*1000*2; //2M
	    	// 设置附件上传目录
	    	$upload->savePath 		= 'uploads/'.$dir.'/'.date('Y').'/'.date('md').'/';
	    	// 上传文件命名规则
	    	$save_rule = date('ymdHis').sprintf('%03d', rand(1, 999));
			$upload->saveRule 		= $save_rule;
			// 存在同名是否覆盖
	    	$upload->uploadReplace 	= true;
			
			if(!is_dir($upload->savePath)){
				if(false === mkdir($upload->savePath, 0700, true)){
					$this->ajaxReturn('', '创建目录失败', 0);
				}
			}
			
			if(!$upload->upload()){
				// 捕获上传异常
				$this->ajaxReturn('', $upload->getErrorMsg(), 0);
			}else{
				// 取得成功上传的文件信息
				$fileinfo_array = $upload->getUploadFileInfo();
				$fileinfo = $fileinfo_array[0];
				
				// 保存上传文件信息到数据库文件表
				$file_data = array();
				$file_data['name'] 		= $fileinfo['name'];
				$file_data['save_path'] = $fileinfo['savepath'];
				$file_data['save_name'] = $fileinfo['savename'];
				$file_data['size'] 		= $fileinfo['size'];
				$file_data['extension'] = $fileinfo['extension'];
				$file_data['create_time'] = time();
				$insert_id = D("File")->add($file_data);
				
				// 获取图片文件尺寸，用于flashupload控件显示<div class="preview"><img width="100" src="/uploads/ad/201307/130704094409248.jpg"></div>
				$imagesize = getimagesize($fileinfo['savepath'].$fileinfo['savename']);
				
				$data = array(
					'file_id'		=> $insert_id,
					'uploaded_url' 	=> $fileinfo['savepath'].$fileinfo['savename'],
					'file_name'		=> $fileinfo['name'],
					'file_extension'=> $fileinfo['extension'],
					'file_size' 	=> byte_format($fileinfo['size']),
					'width'			=> $imagesize[0] ? $imagesize[0] : '',
					'height'		=> $imagesize[1] ? $imagesize[1] : '',
				);
				$this->ajaxReturn($data, '图片上传成功', 1);
			}
				
		}
	}
	
	
	/**
	 * ajax更新enum类型字段值
	 */
	public function toggle_field(){
		if($this->isPost()){
			
			$table 	= $_POST['table'];
			$field 	= $_POST['field'];
			$id 	= intval($_POST['id']);
			$value 	= $_POST['value'];
			
			$data = array();
			$data[$field] = $value;
			$effect = D(ucfirst($table))->where(array('id'=>$id))->save($data);
			if($effect){
				$this->ajaxReturn(null, '更新成功', 1);
			}else{
				$this->ajaxReturn(null, '更新失败', 0);
			}
		}
	}
	
	
	/**
	 * 信息推送get页面结构
	 */
	public function paglet_push(){
		// 暂时仅支持根据模型推荐
		
		$ch = $this->_get('ch');
		if(!$ch){
			// TODO ajax模式,如果返回出错要怎么设计比较好	{"info":"\u53c2\u6570ch\u4e0d\u80fd\u4e3a\u7a7a","status":0,"url":""}
			$this->error('参数ch不能为空');
		}
		
		// 过滤所在模型的推荐位
		$module_id = D("Channel")->where(array('name'=>$ch))->getField('module_id');
		
		$cond = array();
		$cond['module_id'] = $module_id;
		$position_list = D("Position")->where($cond)->field('id,name,max_number')->order('orderid asc,id desc')->select();
		
		// 获取模型名称，自适应调用对应模型的Xxx/push方法
		$module_name = M('Module')->where(array('id'=>$module_id))->getField('name');
		
		
		$this->assign('ch', $ch);
		$this->assign('position_list', $position_list);
		$this->assign('module_name', $module_name);
		$this->display();
	}
	
	/**
	 * ajax更新enum类型字段值
	 * @author fanrong33 <fanrong33#qq.com>
	 */
	public function get_last_selected(){
		
		$model_name = $this->_get('model_name');
		$field = $this->_get('field');
		
		$model_name = decode($model_name, C('AUTH_CODE'));
		$field = decode($field, C('AUTH_CODE'));
		if($model_name && $field){
			$value = D($model_name)->order('id desc')->limit(1)->getField($field);
			if(isset($value)){
				$this->ajaxReturn($value, '获取成功', 1);
			}else{
				$this->ajaxReturn(null, '获取失败, 可能还没有任何数据', 0);
			}
		}
	}
}
?>