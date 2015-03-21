<?php
/**
 +----------------------------------------------------------------------------
 * 系统配置 模型类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v2.0.0 Build 20130327
 +------------------------------------------------------------------------------
 */
class SystemModel extends Model{
	
	protected function _after_update($data,$options) {
		// 更新系统设置删除缓存配置文件
		GS(null);
	}
		
	protected function _after_insert($data,$options) {
		// 新增系统设置删除缓存配置文件
		GS(null);
	}
	
	
	/**
	 * 保存编辑系统配置
	 * @param array $config		POST表单config[]配置信息
	 * @param string $type		系统设置类别
	 * @return boolean 
	 */
	public function edit($config, $type='base'){
		if($config && is_array($config)){
			
			$setting_all = require CONF_PATH.'setting.php';
			$setting = $setting_all[$type];
			
			foreach($config as $key=>$val){
				$data['key']		= $key;
				$data['value'] 		= $val;
				$data['description']= $setting[$key]['title'];
				
				$property = $this->where(array('type'=>$type, 'key'=>$key))->find();
				if($property){ // update
					$effect = $this->where(array('type'=>$type, 'key'=>$key))->save($data);
				}else{ // add
					$data['type']   = $type;
					$effect = $this->add($data);
				}
			}
		}
		
		// 重建缓存？不能在这里，而应该让用的地方去重建，类似懒加载
		return true;
	}
	
	
	/**
	 * 取得配置信息
	 * @param string $type 配置信息类别
	 */
	public function getConfig($type){
		
		$list = $this->where(array('type'=>$type))->select();
		$tmp = array();
		if($list){
			foreach($list as $rs){
				$tmp[$rs['key']] = $rs['value'];
			}
			$list = $tmp;
			unset($tmp);
		}
		return $list;
	}

}


/**
 * TODO
 * 过滤安全性，只有配置文件中的属性才能进行保存
 */
?>