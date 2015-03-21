<?php
/**
 +----------------------------------------------------------------------------
 * CMSPOWER挂件继承父类
 * 前端挂件 需继承自 ParentWidget
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.2 Build 20130711
 +------------------------------------------------------------------------------
 */

// 前端模板默认可以直接使用cmspower开放接口
import('@.ORG.cmspower');

abstract class ParentWidget extends Widget{
	
	protected $_var = ''; // 模板变量
	
	protected $_config = array(); // 挂件模块对应配置
	
	/**
	 * 挂件模块英文名
	 */
	public function render($name){
		
		// 根据挂件模块英文名得到挂件配置
		$this->initConfig($name);
		
		// 模板设计模式
		if(method_exists($this, 'renderWidget')){
			// 前端挂件模块实现该方法，直接可以使用挂件模块对应的配置信息
			$this->renderWidget(); 
		}
		
		$this->assign('_config', $this->_config);
		return $this->renderFile('index');
	}
	
	/**
	 * 用于前端挂件模块初始化配置
	 */
	protected function initConfig($name){
		if($name){
			// 自动定位模板文件
	        $widget_name = substr(get_class($this), 0, -6); // 挂件名 MixWall
			if(!$name){
				throw_exception($widget_name."挂件模块英文名不能为空，无法获取模块对应的配置信息");
			}
	        
	        $template_dir = LIB_PATH.'Widget/'.$widget_name.'/';
	        
	        // 获取挂件的配置文件
	        if(is_file($template_dir.'config.php')){
	        	$config = include $template_dir.'config.php';
	        	$this->_config = $config[$name];
	        }
	        
	        $this->_config = $config[$name];
		}
	}
	
	/**
	 * 模板变量赋值(接口同模板引擎)
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return void
	 */
	protected function assign($name, $value=''){
		if(is_array($name)){
			$this->_var = array_merget($this->_var, $name);
		}else{
			$this->_var[$name] = $value;
		}
	}
	
	protected function renderFile($templateFile='',$var=''){
		$var = $var ? $var : $this->_var;
		return parent::renderFile($templateFile, $var);
	}
	
}
?>