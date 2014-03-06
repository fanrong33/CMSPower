<?php
/**
 +----------------------------------------------------------------------------
 * 更新缓存 控制器类
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33
 * @version v1.0.3 Build 20140211
 +------------------------------------------------------------------------------
 */
class UpdateCacheAction extends AdminCommonAction{
	
	public function index(){
		
		if($this->isPost()){
			if(is_array($_POST['config'])){
				foreach($_POST['config'] as $key => $rs){
					
					if($key == 'cache'){
						// 删除项目模板缓存目录 (修改了头尾部模板)
						rmdir_all(__ROOT__.'Home/Runtime/Cache/');
						rmdir_all(__ROOT__.'Admin/Runtime/Cache/');
						rmdir_all(__ROOT__.'Api/Runtime/Cache/');
					}
					if($key == 'data'){
						// 删除项目数据目录 (修改了数据库结构)
						rmdir_all(__ROOT__.'Home/Runtime/Data/');
						rmdir_all(__ROOT__.'Admin/Runtime/Data/');
						rmdir_all(__ROOT__.'Api/Runtime/Data/');
					}
					if($key == 'runtime'){
						// 删除项目编译缓存文件 (生产环境模式修改了配置文件，需要删除编译缓存文件~runtime.php重建，不然默认不会重建)
						if(is_file(__ROOT__.'Admin/Runtime/'.'~runtime.php')) unlink(__ROOT__.'Admin/Runtime/'.'~runtime.php');
						if(is_file(__ROOT__.'Home/Runtime/'.'~runtime.php')) unlink(__ROOT__.'Home/Runtime/'.'~runtime.php');
						if(is_file(__ROOT__.'Api/Runtime/'.'~runtime.php')) unlink(__ROOT__.'Api/Runtime/'.'~runtime.php');
					}
				}
			}
			$this->success('更新成功');
		}
		
		$this->display();
	}
	
}
?>