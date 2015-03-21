<?php
/**
 +----------------------------------------------------------------------------
 * 采集管理 控制器类
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.1.3 Build 20130924
 +----------------------------------------------------------------------------
 */
class CollectorAction extends AdminCommonAction{
	
	
	/**
	 * 查询采集点
	 */
	public function index(){
		
		$model = D("Collector");
		
		$map = array();
		if(isset($_GET['keyword']) && $_GET['keyword']){
			$keyword = trim($_GET['keyword']);
			$map['title'] = array('like', "%$keyword%");
		}
		$this->_list($model, $map, array('id', 'last_time'), array('desc', 'desc'));
		
		$this->assign('keyword', $keyword);
		$this->display();
		
	}
	
	
	/**
	 * 添加采集点
	 */
	public function add(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			$_validate = array(
				array('title'	, 'require', '采集项目名不能为空'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			// 自定义规则
			$customize_config = $_POST['customize_config'];
			$data['customize_config'] = array();
			if(is_array($customize_config)){
				foreach($customize_config['en_name'] as $k => $v){
					if (empty($v) || empty($customize_config['name'][$k])) {
                        continue;
                    }
                    $data['customize_config'][] = array(
                        'name' 		=> $customize_config['name'][$k],
                        'en_name' 	=> $v,
                        'rule' 		=> stripslashes($customize_config['rule'][$k]),
                        'html_rule' => $customize_config['html_rule'][$k]
                    );
				}
			}
			
			// 对自定义规则进行序列化
            $data['customize_config'] = serialize($data['customize_config']);
           	// 采集地址
           	$data['urlpage'] = $this->_post('urlpage'.$data['source_type']);
           	if(!$data['urlpage']){
           		$this->error('采集网址不能为空');
           	}
			$insert_id = $model->add($data);
			if($insert_id > 0){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		
		$this->display('edit');
	}
	
	
	/**
	 * 修改采集点
	 */
	public function edit(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			
			$_validate = array(
				array('title', 'require', '采集项目名不能为空'),
			);
			
			if(false === $data = $model->validate($_validate)->create()){
				$this->error($model->getError());
			}
			
			// 自定义规则
			$customize_config = $_POST['customize_config'];
			$data['customize_config'] = array();
			if(is_array($customize_config)){
				foreach($customize_config['en_name'] as $k => $v){
					if (empty($v) || empty($customize_config['name'][$k])) {
                        continue;
                    }
                    $data['customize_config'][] = array(
                        'name' 		=> $customize_config['name'][$k],
                        'en_name' 	=> $v,
                        'rule' 		=> stripslashes($customize_config['rule'][$k]),
                        'html_rule' => $customize_config['html_rule'][$k]
                    );
				}
			}
			
			// 对自定义规则进行序列化
            $data['customize_config'] = serialize($data['customize_config']);
           	// 采集地址
           	$data['urlpage'] = $this->_post('urlpage'.$data['source_type']);
			
			$effect = $model->save($data);
			if($effect){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}
		
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('参数id不能为空');
		}
		$collector = $model->find($id);
		if(!$collector){
			$this->error('采集点不存在');	
		}
		$collector['customize_config'] = unserialize($collector['customize_config']);
		
		$this->assign('_model', $collector);
		$this->display();
	}
	
	
	/**
	 * 删除采集点
	 * TODO 删除关联该采集点的所有信息
	 */
	public function delete(){
		$name = $this->getActionName();
		$model = D($name);
		
		if($this->isPost()){
			// 批量删除
			if($_POST['ids']){
				$this->_delete($model, $_POST['ids']);
			}
			$this->ajaxReturn('', '批量删除成功', 1);
		}else{
			// 单个删除
			$id = intval($_GET['id']);
			if(!$id){
				$this->error('参数id不能为空');
			}
			
			$effect = $model->delete($id);
			if($effect){
				$this->success('删除成功');
			}
		}
	}
	
	
	/**
	 * 复制采集项目
	 */
	public function copy(){
		$id = intval($_GET['id']);
    	
		if(!$id){
			$this->error('请指定需要复制的采集节点');
		}
		$collector = D('Collector')->find($id);
		if(!$collector){
			$this->error("该采集节点不存在");
		}
		
		unset($collector['id']);
		unset($collector['name']);
		unset($collector['last_time']);
		$collector['title'] = $collector['title'].'(2)';
		
		$insert_id = D('Collector')->add($collector);
		if($insert_id){
			$this->assign('jumpUrl', U('Collector/index'));
			$this->success('采集节点复制成功');
		}else{
			$this->error('采集节点复制失败');
		}
		
	}
	
	
	/**
	 * 导出采集项目
	 */
    public function export() {
    	$id = intval($_GET['id']);
    	
		if(!$id){
			$this->error('请指定需要复制的采集节点');
		}
		$collector = D('Collector')->find($id);
		if(!$collector){
			$this->error("该采集节点不存在");
		}
		
		unset($collector['id'], $collector['last_time'], $collector['description'], $collector['code'], $collector['version'], $collector['update_time']);
		$collector['name'] = 'suncco_com_news';
		
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $collector['name'] . '.txt');
        echo base64_encode(json_encode($collector));
    }
	
	
	/**
	 * 采集测试 文章URL采集
	 */
    public function public_test() {
    	$id = intval($_GET['id']);
		if(!$id){
			$this->error('参数id不能为空');
		}
		$collector = D('Collector')->find($id);
		if(!$collector){
			$this->error("该采集节点不存在");
		}
		
		import('@.ORG.Net.Collector');
		$url_list = Collector::url_list($collector, 1);
		if($url_list){
			foreach($url_list as $rs){
				// 获取文章网址
				$url = Collector::get_url_lists($rs, $collector);
			}
		}
		
		$this->assign('list', $url);
		$this->display();
    }
	
	
	public function item(){
		
//		$content = file_get_contents('http://lhkjnb.m.tmall.com/shop/a-5-5-42-91965143.htm');
//		$content = file_get_contents('http://www.zcool.com.cn/works/17!0!0!0!0!200!1!1/');
		
		// 匹配url地址
//		preg_match_all('/<div><a href=\"(.*?)\">/si', $content, $matches);
//		dump($matches);

		// 匹配图片地址src
		$content = '<img width=100 src=\'http://q.i04.wimg.taobao.com/bao/uploaded/i3/15143027128307885/T1wjpEFlldXXXXXXXX_!!0-item_pic.jpg_100x100.jpg\' alt="Asus/华硕 A550 A550X3217CC-SL/84FRDX1W I3独显2G笔记本 送耳麦" />';
		preg_match('/<img[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/ie', $content, $matches);
		dump($matches);
		
		exit;
		
		$data['content'] = stripslashes(preg_replace('/<img[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/ie', "self::download_img('$0', '$1')", $data['content']));
		
		
		
		// 网址中必须包含
		$url_contain = 'imgcdn.zcool.com.cn';
		if($matches[1] && $url_contain){
			// 过滤网址
			$tmp_list = array();
			foreach($matches[1] as $rs){
				if(strpos($rs, $url_contain) === false){
					continue;
				}
				$tmp_list[] = $rs;
			}
			$matches[1] = $tmp_list;
			unset($tmp_list);
		}
		dump($matches);
		
		import('@.ORG.Net.Http');
		foreach($matches[1] as $rs){
			$local_file = time().'_'.sprintf('%04d', rand(1, 9999)).'.'.end(explode('.', $rs));
			// 采集远程文件
			Http::curl_download($rs, $local_file);
		}
	}
	
	
	/**
	 * 采集网址
	 * 采集第一步，采集网址入库
	 */
    public function col_url_list() {
    	
    	$id = intval($_GET['id']);
		if(!$id){
			$this->error('请指定采集节点');
		}
		$collector = D('Collector')->find($id);
		if(!$collector){
			$this->error("该采集节点不存在");
		}
    	
    	
    	// 获取网页列表页
    	import('@.ORG.Net.Collector');
    	$url_list = Collector::url_list($collector);
    	
    	if(is_array($url_list)){
    		$total_page = count($url_list);
    	}else{
    		$total_page = 0;
    	}
    	
    	if($total_page <= 0){
    		$this->error("没有内容可采集");
    	}
    	
    	$page = intval($_GET['page']) ? intval($_GET['page']) : 0;
    	$url = $url_list[$page];
    	
    	// 获取文章网址
    	$article_url_list = Collector::get_url_lists($url, $collector);
    	
    	$CollectorHistory = D('CollectorHistory');
    	$CollectorData = D('CollectorData');
    	
    	// 待采集文章的总数
    	if(is_array($article_url_list)){
    		$article_total = count($article_url_list);
    	}else{
    		$article_total = 0;
    	}
    	// 重复记录
    	$repeat_total = 0;
    	
    	if(is_array($article_url_list) && !empty($article_url_list)){
    		foreach($article_url_list as $rs){
    			if(empty($rs['url']) || empty($rs['title'])){
    				continue;
    			}
    			
    			// 对URL地址进行MD5加密当作标识符
                $md5 = md5($rs['url']);
                
    			// 检测是否已经存在
                if ($CollectorHistory->where(array('md5' => $md5, 'collector_id' => $id))->find()) {
                	$repeat_total++;
                } else {
                    // 添加MD5值到历史记录
                    $data = array();
                    $data['md5'] 			= $md5;
                    $data['collector_id'] 	= $id;
                    $CollectorHistory->add($data);
                    
                    $data = array();
                    $data['collector_id'] 	= $id;
                    $data['status'] 		= '1'; // 未采集
                    $data['url'] 			= $rs['url'];
                    $data['title'] 			= strip_tags($rs['title']); // 去除HTML
                    $CollectorData->add($data);
                }
    		}
    	}
    	
    	// 更新最后采集时间
    	if($total_page <= $page){
	        D('Collector')->where(array('id' => $id))->save(array('last_time' => time()));
    	}
        
        $this->assign('collector', $collector);
        $this->assign('article_url_list', $article_url_list);
        $this->assign('article_total', $article_total);
        $this->assign("repeat_total", $repeat_total);
        $this->assign("page", $page);
        //由于数组是从0开始，但是根据这个总数和页数进行判断是否继续下一页，所以减一
        $this->assign("total_page", $total_page-1);
        $this->display();
    }
	
	
	/**
	 * 采集内容
	 * 采集第二部，内容采集入库
	 */
	public function col_content(){
		$id = intval($_GET['id']);
		if(!$id){
			$this->error('请指定采集节点');
		}
		$collector = D('Collector')->find($id);
		if(!$collector){
			$this->error("该采集节点不存在");
		}
		
		$CollectorData = D('CollectorData');
		
		$page = intval($_GET['page'])>0 ? intval($_GET['page']) : 1;
		$total = intval($_GET['total'])>0 ? intval($_GET['total']) : 0;
		if(empty($total)){
			// 统计该采集节点未采集文章的数量
			$total = $CollectorData->where(array('collector_id'=>$id, 'status'=>'1'))->count();
		}
		
		// 每次采集1条，分页总数
		import('@.ORG.Net.Collector');
		$total_page = ceil($total / 1); // output: 33
		$collector_data = $CollectorData->field('id,url')->where(array('collector_id'=>$id, 'status'=>'1'))->order("id desc")->find();
		
		if($collector_data){
			// 获取采集内容
            $html = Collector::get_content($collector_data['url'], $collector);
            
            $CollectorData->where(array('id'=>$collector_data['id']))->save(array('status'=>'2', 'data'=>serialize($html)));
		}else{
			D('Collector')->where(array('id'=>$id))->save(array('last_time'=>time()));
			
			$this->assign("waitSecond", 3);
			$this->success('内容采集完成', U('Collector/index'));
			exit;
		}
		
        // 如果总分页数大于当前分页数，则进行下一轮采集
        if ($page <= $total_page) {
            $this->assign("waitSecond", 100);
            $this->assign('jumpUrl', U("Collector/col_content", array("id" => $id, "page" => $page+1, "total" => $total)));
            $this->success("正在采集，采集进度: ".($page*1).'/'.$total."，完成进度：".sprintf('%.1f%%',$page/$total*100));
        }
	}
	
	
	/**
	 * 内容发布
	 */
    public function publist() {
    	
    	$id = intval($_GET['id']);
    	$status = intval($_GET['status']);
    	
		if(!$id){
			$this->error('参数id不能为空');
		}
		$collector = D('Collector')->find($id);
		if(!$collector){
			$this->error("该采集节点不存在");
		}
		
		$map = array();
		$map['collector_id'] = $id;
		if($status){
			$map['status'] = $status;
		}
		
		$this->_list(D('CollectorData'), $map, 'id', 'desc');
		
		$this->display();
    }
	
	
	/**
	 * 文章导入
	 */
    public function import() {
    	
    	$id = intval($_GET['id']);
    	$type = $_GET['type']; // 导入类型
    	
		if(!$id){
			$this->error('参数id不能为空');
		}
		$collector = D('Collector')->find($id);
		if(!$collector){
			$this->error("该采集节点不存在");
		}

        // 获取发布方案列表
      	$program_list = D("CollectorProgram")->where(array('collector_id' => $id))->select();
		
        //栏目
        $channel_list = D('Channel')->where(array('level'=>array('elt', 2)))->order('orderid asc')->select();
        foreach($channel_list as $k => $rs){
        	if($rs['pid'] == 0){
        		$channel_list[$k]['disabled'] = 'disabled';
        	}else{
        		$channel_list[$k]['disabled'] = '';
        	}
        }
		$channel_list = array_key_list($channel_list, 'id');
		
		// 创建树状 option string
		import('@.ORG.Form.Tree');
		$tree = new tree();
		$tree->init($channel_list);
		$str = "<option value=\$id \$selected \$disabled>\$spacer \$title</option>";
        
        
        $this->assign('options', $tree->get_tree(0, $str, 0));
        $this->assign("program_list", $program_list);
        $this->assign("type", $type);
        $this->display();
    }
	
	
	/**
	 * 导入文章到模型
	 */
	public function import_content(){
		
		$id = intval($_GET['id']);
		$program_id = intval($_GET['program_id']);
		if(!$id){
			$this->error('请指定采集节点');
		}
		if(!$program_id){
			$this->error('请指定发布方案');
		}
		
		$collector = D('Collector')->find($id);
		if(!$collector){
			$this->error("该采集节点不存在");
		}
		
		// 导入顺序，与目标站相同
		$order = $collector['coll_order'] == 1 ? 'id desc' : '';
		
		// 全部导入
		$cond = array();
		$cond['collector_id'] = $id;
		$cond['status'] = '2';
		
		$total = intval($_GET['total']) ? intval($_GET['total']) : '';
		if(empty($total)){
			$total = D('CollectorData')->where($cond)->count();
		}
		// 导入轮数，每次20条入库
		$total_page = ceil($total / 20);
		$page = intval($_GET['page']) ? intval($_GET['page']) : 1;
		// 获取待导入模型的采集数据
		$data_list = D('CollectorData')->field('id,title,url,data')->where($cond)->order($order)->limit(20)->select();
		
		// 获取方案
		$program = D('CollectorProgram')->find($program_id);
        $program['config'] = unserialize($program['config']);
        
        // 根据发布方案获取待导入模型名
		$module_name = D('Module')->where(array('id'=>$program['module_id']))->getField('name');
		
		$i = 0;
		$imported_ids = array();
		// 内容处理
		foreach($data_list as $key => $rs){
			
			$rs['data'] = unserialize($rs['data']);
			
			
			$data = array();
			$data['channel_id'] = $program['channel_id'];
//			$data['is_show'] = '1'; // 审核通过？
			
			// 如果值不存在或者为空。则填充默认数据
			foreach($program['config']['default'] as $k => $b){
                if (empty($rs['data'][$k]) && isset($program['config']['default'][$k])) {
                    $rs['data'][$k] = $program['config']['default'][$k];
                }
			}
			
			// 根据对应关系补充数据
			foreach($program['config']['map'] as $module_field => $collector_field){
				
				// 检查是否需要进行自定义处理函数
				if(isset($program['config']['funcs'][$module_field]) && function_exists($program['config']['funcs'][$module_field])){
					try{
						$data[$module_field] = call_user_func($program['config']['funcs'][$module_field], $rs['data'][$collector_field]?$rs['data'][$collector_field]:$rs['data'][$module_field]);
					}catch(Exception $e){
						$data[$module_field] = $rs['data'][$collector_field]?$rs['data'][$collector_field]:$rs['data'][$module_field];
					}
				}else{
					$data[$module_field] = $rs['data'][$collector_field] ? $rs['data'][$collector_field] : $rs['data'][$module_field];
				}
				
			}
			
			$insert_id = D($module_name)->add($data);
			if($insert_id){
				$imported_ids[] = $rs['id'];
				$i++;
			}
		}
		
		// 更新采集状态
		D('CollectorData')->where(array('id'=>array('in', $imported_ids)))->save(array('status'=>'3'));
		if($page < $total_page ){
			$this->assign('waitSecond', 200);
            $this->assign('jumpUrl', U('Collector/import_content', array('id'=>$id,'program_id'=>$program_id, 'page'=>$page+1, 'total'=>$total)));
            $this->success('正在导入中，导入进度：'.(($page - 1) * 20 + $i) . '/' . $total);
		}else{
			$this->assign('jumpUrl', U('Collector/publist', array('id'=>$id, 'status'=>2)));
			$this->success('导入成功');
		}
	}
	
	
	
	/**
	 * 添加导入方案
	 */
    public function import_program_add() {
    	
    	$id 		= intval($_GET['id']);
    	$type 		= $_GET['type']; // 导入类型
		$channel_id = intval($_GET['channel_id']);
    	
		if(!$id){
			$this->error('参数id不能为空');
		}
		if(!$channel_id){
            $this->error("请指定栏目");
		}
		
		$collector = D('Collector')->find($id);
		if(!$collector){
			$this->error("该采集节点不存在");
		}
		$collector['customize_config'] = unserialize($collector['customize_config']);
		
		
		if($this->isPost()){
			$config = array();
			$module_fields 		= $_POST['module_fields'];
			$collector_fields 	= $_POST['collector_fields'];
			
			if(!$module_fields || !$collector_fields){
				$this->error('参数错误');
			}
			$funcs = $_POST['funcs'] ? $_POST['funcs'] : array();
			
			// 模型字段和入库数据对应关系
			foreach($collector_fields as $k => $v){
				if(empty($v)){
					continue;
				}
				$config['map'][$module_fields[$k]] = $v;
			}
			
			// 自定义处理函数
			foreach($funcs as $k => $v){
				if(empty($v)){
					continue;
				}
				$config['funcs'][$module_fields[$k]] = $v;
			}
			
			// 默认值
			$default = $_POST['default'] ? $_POST['default'] : array(); 
			foreach($default as $k => $v){
				if(empty($v)){
					continue;
				}
				$config['default'][$module_fields[$k]] = $v;
			}
			
			
			$module_id = D('Channel')->where(array('id'=>$channel_id))->getField('module_id');
			
			// 添加导入方案
			$data = array();
			$data['name'] 		  = trim($this->_post('name'));
			$data['collector_id'] = $id;
			$data['channel_id']   = $channel_id;
			$data['module_id']    = $module_id;
			$data['config']       = serialize($config);
			
			$insert_id = D('CollectorProgram')->add($data);
			if($insert_id){
				$this->success('发布方案添加成功', U('Collector/import_content', array('id'=>$id,'program_id'=>$insert_id)));
			}else{
				$this->error('发布方案添加失败');
			}
			
		}
		
		
        // 获取栏目所属数据模型的字段信息
        $module_id = D('Channel')->where(array('id'=>$channel_id))->getField('module_id');
        $module = D('Module')->find($module_id);
        if(!$module){
        	$this->error('模型不存在');
        }
        
        $module_field_list = $this->getDbFields($module['name']);
        
        // 获取采集的内容规则，注入数据模型中
        $collector_field_list = array('' => "请选择", 'title' => "标题", 'author' => "作者", 'comeform' => "来源", 'time' => "时间", 'content' => "内容");
        if(is_array($collector['customize_config'])){
        	foreach($collector['customize_config'] as $k => $v){
        		if(empty($v['en_name']) || empty($v['name'])){
        			continue;
        		}
        		$collector_field_list[$v['en_name']] = $v['name'];
        	}
        }
        
        $this->assign('collector', $collector);
        $this->assign('module_field_list', $module_field_list);
        $this->assign('collector_field_list', $collector_field_list);
        $this->assign('type', $type);
        
        $this->display();
    }
    
    
    /**
     * 取得数据表的字段信息
     */
    private function getDbFields($ModelName) {
    	$table_name = M($ModelName)->getTableName();
    	
        $result =   M($ModelName)->query('SHOW FULL COLUMNS FROM '.$table_name);
        $info   =   array();
        if($result) {
            foreach ($result as $key => $val) {
                $info[$val['Field']] = array(
                    'name'    => $val['Field'],
                    'type'    => $val['Type'],
                    'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                    'default' => $val['Default'],
                    'primary' => (strtolower($val['Key']) == 'pri'),
                    'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
                    'comment' => $val['Comment'], // 增加字段注释信息
                );
            }
        }
        return $info;
    }
    
    
    /**
	 * 显示添加采集节点对话框
	 */
	public function paglet_collector(){
		// 调用接口获取云商店所有应用列表
		import("@.ORG.Api.Cloudstore");
		
		$cloudstore = new cloudstorePHP();
		$json = $cloudstore->get_collector_list();
		if($json['status']==1){
			$this->assign('collector_list', $json['data']);
		}
		
		// 获取系统已安装内容模块列表信息
		$installed_list = D("Collector")->select();
		$installed_map = array_key_list($installed_list, 'name');
		$this->assign('installed_map', $installed_map);
				
		$this->display();
	}
    
    
    /**
	 * 安装采集节点到后台
	 */
	public function install_collector(){
		if($this->isPost()){
			$collector_id = intval($_POST['collector_id']);
			
			// collector name必须唯一，安装过就不能再安装
			// 调用接口获取云商店对应id应用
			import("@.ORG.Api.Cloudstore");
			
			$cloudstore = new cloudstorePHP();
			$json = $cloudstore->get_collector($collector_id);
			$collector = $json['data'];
			
			$installed_collector = D("Collector")->getByName($collector['name']);
			if($installed_collector){
				if($installed_collector['code'] >= $collector['code']){
					$this->ajaxReturn(null, '已添加该采集节点', 0);
				}
			}
			
			// 下载collector txt文本文件到临时目录
			import("@.ORG.Net.Http");
			$response = Http::fsockopen_download($collector['zip_url']);
			
			$collector_zip = TEMP_PATH.$collector['name'].'.txt';
			$write_bytes = file_put_contents($collector_zip, $response);
			if(!$write_bytes){
				unlink($collector_zip);
				$this->ajaxReturn(null, '写入文件失败', 0);
			}
			if($collector['file_size'] != $write_bytes){
				$this->ajaxReturn(null, '采集节点安装包下载失败，可能文件不存在', 0);
			}
			
			
			// 读取文件
			$data = json_decode(base64_decode(file_get_contents($collector_zip)), true);
			unset($collector['id']);
			$data = array_merge($data, $collector);
			
			// 安装完成时自动删除下载的安装包
			unlink($collector_zip);
			
			if(!$installed_collector){
				// 1、安装采集节点
				
				// 写入已安装采集节点数据表中
				$insert_id = D("Collector")->add($data);
				 
				$result = array();
				$result['id'] 	= $insert_id;
				$this->ajaxReturn($result, '安装成功', 1);
			}else{
				if($installed_collector['code'] < $collector['code']){
					
					// 2、升级已安装采集节点的信息
					D("Module")->where(array('id'=>$installed_collector['id']))->save($data);
					
					$nav = array();
					$nav['update'] 	= true;
					$this->ajaxReturn($nav, '升级成功', 1);
				}
			}
		}
	}
    
    
}
?>