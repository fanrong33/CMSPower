<?php

/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @author Anyon Zou <cxphp@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-01-21 28:28
 * @return String
 */
function encode($string='', $skey ='cmspower') {
    $skey = str_split(base64_encode($skey));
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach ($skey as $key => $value) {
        $key < $strCount && $strArr[$key].=$value;
    }
    return str_replace('=', 'O0O0O', join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @author Anyon Zou <cxphp@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-01-21 28:28
 * @return String
 */
function decode($string='', $skey='cmspower') {
    $skey = str_split(base64_encode($skey));
    $strArr = str_split(str_replace('O0O0O', '=', $string), 2);
    $strCount = count($strArr);
    foreach ($skey as $key => $value) {
        $key < $strCount && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    }
    return base64_decode(join('', $strArr));
}

/**
 * 获取域名
 */
function get_domain(){
    /* 协议 */
    $protocol = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';

    /* 域名或IP地址 */
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
    } elseif (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    } else {
        /* 端口 */
        if (isset($_SERVER['SERVER_PORT'])) {
            $port = ':' . $_SERVER['SERVER_PORT'];
            if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol)) {
                $port = '';
            }
        } else {
            $port = '';
        }

        if (isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'] . $port;
        } elseif (isset($_SERVER['SERVER_ADDR'])) {
            $host = $_SERVER['SERVER_ADDR'] . $port;
        }
    }

    return $protocol . $host;
}


/**
 * 判断字符编码是否为UTF8
 */
function is_utf8($content){
	if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$content) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$content) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$content) == true){
		return true;
	}else{
		return false;
	}
}

/**
 * 取得URL地址中域名部分
 * @param type $url 
 * @return \url 返回域名
 */
function get_url_domain($url) {
    if ($url) {
        $pathinfo = parse_url($url);
        return $pathinfo['scheme'] . "://" . $pathinfo['host'] . "/";
    }
    return false;
}

/**
 * 获取当前页面完整URL地址
 * @return type 地址
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
    $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . safe_replace($_SERVER['QUERY_STRING']) : $path_info);
    return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

/**
 * 安全过滤函数
 * @param $string
 * @return string
 */
function safe_replace($string) {
    $string = str_replace('%20', '', $string);
    $string = str_replace('%27', '', $string);
    $string = str_replace('%2527', '', $string);
    $string = str_replace('*', '', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace('"', '', $string);
    $string = str_replace(';', '', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    $string = str_replace("{", '', $string);
    $string = str_replace('}', '', $string);
    $string = str_replace('\\', '', $string);
    return $string;
}

/**
 * 对URL中有中文的部分进行编码处理
 * @param type $url 地址 http://www.abc3210.com/s?wd=博客
 * @return type ur;编码后的地址 http://www.abc3210.com/s?wd=%E5%8D%9A%20%E5%AE%A2
 */
function cn_urlencode($url) {
    $pregstr = "/[\x{4e00}-\x{9fa5}]+/u"; //UTF-8中文正则
    if (preg_match_all($pregstr, $url, $matchArray)) {//匹配中文，返回数组
        foreach ($matchArray[0] as $key => $val) {
            $url = str_replace($val, urlencode($val), $url); //将转译替换中文
        }
        if (strpos($url, ' ')) {//若存在空格
            $url = str_replace(' ', '%20', $url);
        }
    }
    return $url;
}






/**
 * 搜索高亮显示关键字
 * 
 * @param string $string 原字符串
 * @param string $keyword 搜索关键字，默认为keyword, 可不传
 * 
 * @return string $string 返回高亮后的字符串
 */
function highlight($string, $keyword=''){
	if($keyword == ''){
		$keyword = 'keyword'; // 默认搜索关键字为 keyword
	}
	
	if(isset($_GET[$keyword]) && $_GET[$keyword]){
		
		$keyword_value = $_GET[$keyword];
		return preg_replace("/($keyword_value)/i", "<span style=\"color:#FF0000\">\\1</span>", $string);
	}
	return $string;
}


/**
 * 密码加密方法
 */
function encrypt_pwd($password){
    //TODO 暂时在后端进行加密，为前端未来加密传输预留
    $password = md5($password);
    return md5(crypt($password, substr($password, 0, 2)));
}


/**
 * 根据不同值显示对应的状态
 * @belongs CMSPOWER
 */
function to_status($type, $value){
	$map = array(
		 'channel' => array(1=>'内部栏目', 2=>'单网页', 3=>'外部链接'),
	);
	return $map[$type][$value];
}



/**
 +----------------------------------------------------------
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function byte_format($size, $dec=2){
	$a = array("B", "KB", "MB", "GB", "TB", "PB");
	$pos = 0;
	while ($size >= 1024) {
		$size /= 1024;
	    $pos++;
	}
	return round($size,$dec)." ".$a[$pos];
}


/**
 * 返回两日期时间差运行时间，格式：几天几小时几分几秒
 */
function uptime($uptime){
    $result = '';
//    $start = strtotime($time1);
//    $end = strtotime($time2);
//    $cha = $end - $start;

    //求天数
    $days = intval(($uptime / 3600) / 24);
    $result .= $days.'天';
    
    //86400 是一天
    $num2 = $days*86400;
    $hours = intval(($uptime - $num2)/3600);
    $result .= $hours.'小时';
    
    // 3600 是一小时
    $num3 = $hours*3600;
    $minutes = intval(($uptime - $num2 - $num3) / 60);
    $result .= $minutes.'分';
    
    // 60 是一分钟
    $num4 = $minutes * 60;
    $seconds = intval(($uptime - $num2 - $num3 - $num4) / 1);
    $result .= $seconds.'秒';
    return $result;
}


/**
 * 将索引数组转化为以某键的值为索引的数组
 * @param array $list 要进行转换的数据集
 * @param string $key 以该key为索引
 * 
 */
function array_key_list($list, $key='id'){
	$result = array();
	if(is_array($list)){
		foreach($list as $rs){
			$result[$rs[$key]] = $rs;
		}
	}
	return $result;
}


/**
 * 把返回的数据集转换成Tree
 * 
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0){
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 +----------------------------------------------------------
 * 把返回的数据集转换成Tree,并将$field作为key
 +----------------------------------------------------------
 */
function list_to_tree_key($list , $field = 'id', $pk = 'id' , $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array ();
    if (is_array ( $list )) {
        // 创建基于主键的数组引用
        $refer = array ();
        foreach ( $list as $key => $data ) {
            $refer [$data [$pk ]] = & $list [$key];
        }
        foreach ( $list as $key => $data ) {
            // 判断是否存在parent
            $parentId = $data [$pid ];
            if ($root == $parentId) {
                $tree [$data [$field ]] = & $list [$key];
            } else {
                if (isset ( $refer [$parentId] )) {
                    $parent = & $refer [$parentId ];
                    $parent [$child ] [$data [$field]] = & $list [$key ];
                }
            }
        }
    }
    return $tree ;
}

/**
 +----------------------------------------------------------
 * 自动转换字符集 支持数组转换
 * 需要 iconv 或者 mb_string 模块支持
 * 如果 输出字符集和模板字符集相同则不进行转换
 +----------------------------------------------------------
 * @param string $fContents 需要转换的字符串
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function auto_charset($fContents, $from='gbk', $to='utf-8') {
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
}

/**
 * 循环创建目录
 */
function mk_dir($dir, $mode = 0777) {
    if (is_dir($dir) || @mkdir($dir, $mode))
        return true;
    if (!mk_dir(dirname($dir), $mode))
        return false;
    return @mkdir($dir, $mode);
}

/**
 * 删除文件夹及其文件夹下所有文件
 *
 */
function rmdir_all($dirName){
	if(! is_dir($dirName)){
		@unlink($dirName);
        return false;
    }
    $handle = @opendir($dirName);
    while(($file = @readdir($handle)) !== false){
		if($file != '.' && $file != '..'){
        	$dir = $dirName . '/' . $file;
            is_dir($dir) ? rmdir_all($dir) : @unlink($dir);
        }
    }
    closedir($handle);

    return rmdir($dirName);
}


/**
 * 如何判断一个目录有没有文件
 */
function dir_is_empty($dir){
	if($handle = opendir("$dir")){
		while($item = readdir($handle)){
			if ($item != "." && $item != "..") return false;
		}
	}
	return true;
}


/**
 +----------------------------------------------------------------------------
 * UI控件相关函数
 +------------------------------------------------------------------------------
 */

/**
 * 开关启用状态控件
 * @param string $model_name 	 模型名称
 * @param string $field		 	 要开关的字段
 * @param integer $id		 	 对象id
 * @param integer $value	 	 当前值
 * @param string $open_iconfont  启用图标字体
 * @param string $close_iconfont 禁用图标字体
 * @author fanrong33
 * @version v1.0.0 Build 20140401
 */
function html_toggle_field($model_name, $field, $id, $value, $open_iconfont='&#x4b;', $close_iconfont='&#x42;'){
	ob_start();
	$html = '';
	
	$html = ''.
		'<a href="javascript:;" onclick="toggle_field(this);"'.
		'	data-model="'.$model_name.'"'.
		'	data-field="'.$field.'"'.
		'	data-id="'.$id.'"'.
		'	data-value="'.$value.'"'.
		'	data-open-iconfont="'.$open_iconfont.'"'.
		'	data-close-iconfont="'.$close_iconfont.'"'.
		'	>';
	if($value == 1){
		$html .= '<i class="iconfont">'.$open_iconfont.'</i>';
	}else{
		$html .= '<i class="iconfont">'.$close_iconfont.'</i>';
	}
	
	$html .= '</a>';
	
	echo $html;
	return ob_get_clean();
}

/**
 * 生成排序链接，暂不支持搜索条件
 * @param string $order_by
 * @param string $default_direction
 * @param string $text
 * @author fanrong33
 * @version v1.0.1 Build 20140304
 * 
 * {:html_order('orderid', 'desc', '显示顺序')}
 */
function html_order($order_by, $default_direction, $text){
	if(isset($_REQUEST['order_by']) && isset($_REQUEST['direction'])){
 		$sort = $_REQUEST['direction'];
 		$sort = in_array($sort, array('desc', 'asc')) ? $sort : 'desc';
	 	$sort = ($sort == 'asc') ? 'desc' : 'asc';
		$sort_alt = ($sort == 'asc') ? '↓' : '↑'; // 排序方式
	}
	
	$count_direction = ($_REQUEST['order_by'] == $order_by) ? $sort : $default_direction; // 默认点击的方向
	$count_alt = ($_REQUEST['order_by'] == $order_by) ? $sort_alt : '';
	
	$url = U(MODULE_NAME/ACTION_NAME, array('order_by'=>$order_by, 'direction'=>$count_direction));
	echo "<a href='{$url}'>{$text}{$count_alt}</a>";
}

/**
 * 点击自动获取上一个select选择的选项
 * @param string $trigger	 触点元素节点，仅支持一个 id 或 class 选择器
 * @param string $model_name 模型名
 * @param string $field      要获取的上一个的字段
 * @depend jquery.select.js
 */
function html_get_last_selected($trigger, $model_name, $field){
	if(ACTION_NAME == 'add'){
		ob_start();
		
		// 对$model_name和$field进行简单对称加密
		$model_name = encode($model_name, C('AUTH_CODE'));
		$field 		= encode($field, C('AUTH_CODE'));
		
		$html = "&nbsp;<a href=\"javascript:;\" onclick=\"get_last_selected('$trigger', '$model_name', '$field')\" title=\"获取上一个选择的选项\">点击自动获取</a>";
		
		echo $html;
		return ob_get_clean();
	}
}

/**
 * 上传附件控件
 * @param string $field  	字段
 * @param string $dir 	 	目录
 * @param boolean $multi 	是否多文件
 * @param string $mode 	 	preview/list
 * @param string $exts 		支持的文件后缀（以逗号,分割）
 * @param string $explain	字段说明
 * @param mix $_model		对象变量
 */
function html_uploadify($field, $dir='sw', $multi=false, $mode='preview', $exts='jpg,jpeg,png', $explain='图片尺寸：550x440', $_model){
	$multi_str = $multi ? 'true' : 'false';
	// 对dir和exts进行简单对称加密
	$dir 	= encode($dir, C('AUTH_CODE'));
	$exts 	= encode($exts, C('AUTH_CODE'));
	
	static $is_load = false;
	
	ob_start();
	$css = JS_PATH . 'thirdparty/uploadify/uploadify.css';
	$js = JS_PATH .'thirdparty/uploadify/jquery.uploadify-3.1.min.js';
	
	$html = '';
	if($is_load === false){
		// 只加载一次资源文件
		$html .= '<link rel="stylesheet" href="'.$css.'" />';
		$html .= '<script type="text/javascript" src="'.$js.'"></script>';
		
		$is_load = true;
	}
	
	$html .= "<a href='javascript:;' onclick='show_upload_dialog(\"$field\", \"$dir\", $multi_str, \"$mode\", \"$exts\");' class='btn btn-small'>添加附件</a>";
	$html .= "<p class='form-explain'><span id='file_upload_$field'>$explain</span></p>";
	
	if($mode == 'list'){
		
		$html .= ''.
			'<div id="cmspower_uploaded_'.$field.'">'.
			'	<ul class="uploaded-list">';
		if($multi){
			if(isset($_model)){
				if(!$_model[$field.'_file_list']){
					throw_exception('$_model变量中必须包含附件'.$field.'_file_list数组，否则无法获取附件相关信息');
				}
				foreach($_model[$field.'_file_list'] as $file){
					$html .= ''.
						'<li>'.
						'	<input type="hidden" name="'.$field.'[]" value="'.$file['save_path'].$file['save_name'].'">'.
						'	<input type="hidden" name="'.$field.'_file_id[]" value="'.$file['file_id'].'">'.
						'	<i class="icon-file-'.get_file($file['file_id'], 'extension').' mr5"></i>'.
						'	<span><a href="/'.$file['save_path'].$file['save_name'].'" style="text-decoration: none;">'.get_file($file['file_id'], 'name').'</a></span>'.
						'	<span class="txt-gray">('.byte_format(get_file($file['file_id'], 'size')).')</span>'.
						'	<a class="delete" href="javascript:;" onclick="delete_attachment(this);"><i class="iconfont">&#x49;</i></a>'.
						'</li>';
				}
			}
		}else{
			if(isset($_model)){
				if(!$_model[$field.'_file_id']){
					throw_exception('$_model变量中必须包含附件'.$field.'_file_id字段，否则无法获取附件相关信息');
				}
				$html .= ''.
					'<li>'.
					'	<input type="hidden" name="'.$field.'" value="'.$_model[$field].'">'.
					'	<input type="hidden" name="'.$field.'_file_id" value="'.$_model[$field.'_file_id'].'">'.
					'	<i class="icon-file-'.get_file($_model[$field.'_file_id'], 'extension').' mr5"></i>'.
					'	<span><a href="/'.$_model[$field].'" style="text-decoration: none;">'.get_file($_model[$field.'_file_id'], 'name').'</a></span>'.
					'	<span class="txt-gray">('.byte_format(get_file($_model[$field.'_file_id'], 'size')).')</span>'.
					'	<a class="delete" href="javascript:;" onclick="delete_attachment(this);"><i class="iconfont">&#x49;</i></a>'.
					'</li>';
			}
		}
		$html .= ''.
			'	</ul>'.
			'</div>';
	}
	if($mode == 'preview'){
		$html .= ''.
			'<div id="cmspower_uploaded_'.$field.'">'.
			'	<ul class="uploaded-preview">';
		if($multi){
			
		}else{
			if(isset($_model)){
				$html .= ''.
					'<li>'.
					'	<input type="hidden" name="'.$field.'" value="'.$_model[$field].'">'.
					'	<input type="hidden" name="'.$field.'_file_id" value="'.$_model[$field.'_file_id'].'">'.
					'	<div class="preview"><img src="/'.$_model[$field].'" width="100"></div>'.
					'	<a class="delete" href="javascript:;" onclick="delete_attachment(this);"><i class="iconfont">&#x49;</i></a>'.
					'</li>';
			}
		}
		$html .= ''.
			'	</ul>'.
			'</div>';
	}
	
	echo $html;
	return ob_get_clean();
}

/**
 * 获取文件的相关信息
 * @param integer $file_id  文件ID
 * @param string $field		文件字段
 */
function get_file($file_id, $field){
	$file = D("File")->find($file_id);
	return isset($file[$field]) ? $file[$field] : '';
}

/**
 * 富文本编辑器
 * 
 * @param string $name		表单输入域
 * @param string $value		textarea的值
 * @param integer $width	显示的宽度
 * @param integer $height	显示的高度
 */
function html_editor($name='content', $value='', $width='640', $height=300){
	static $is_load = false, $index = 1;
	
	ob_start();
	$js = JS_PATH.'thirdparty/KindEditor/kindeditor-min.js';
	$id = 'EDITOR_'.time(); 	// 编辑器ID
	$editor = 'editor'.$index; 	// 编辑器对象
	$index++;
	
	$html = '';
	if ($is_load === false){
		$html .= '<script type="text/javascript" src="'.$js.'"></script>';
		$is_load = true;
	}
	
	$html .= '<textarea id="'.$id.'" name="'.$name.'" style="width:'.$width.'px;height:'.$height.'px;">'.htmlspecialchars($value).'</textarea>';
	$html .= ''.
	'<script type="text/javascript">'.
	'	var '.$editor.';'.
	'	KindEditor.ready(function(K) {'.
	'	'.$editor.' = K.create("#'.$id.'", {'.
	'			pasteType: 1,'.
	'			newlineTag : "p",'.
	'			resizeType : 1,'.
	'			allowPreviewEmoticons : false,'.
	'			allowImageUpload : true,'.
	'			afterBlur: function(){this.sync();},'.
	'			items : ['.
	'				"source", "|" , "preview", "plainpaste", "wordpaste", "|", "formatblock", "fontname", "fontsize", "|", "forecolor", "hilitecolor", "bold", "italic", "underline",'.
	'				"removeformat", "|", "justifyleft", "justifycenter", "justifyright", "insertorderedlist",'.
	'				"insertunorderedlist", "|", "image", "multiimage", "baidumap", "link", "unlink" ,"pagebreak", "|", "fullscreen"]'.
	'		});'.
	'	});'.
	'</script>';
	
	echo $html;
	return ob_get_clean();
}


/**
 +----------------------------------------------------------------------------
 * 缓存相关函数
 +------------------------------------------------------------------------------
 */
 
 
/**
 * 快速文件数据读取和保存 针对简单类型数据 字符串、数组
 * 生成PHP文件，相对比较安全，浏览器无法下载
 */
function GF($name, $value='', $path=DATA_PATH) {
    static $_cache = array();
    $filename = $path . $name . '.php';
    if ('' !== $value) {
        if (is_null($value)) {
            // 删除缓存
            return unlink($filename);
        } else {
            // 缓存数据
            $dir = dirname($filename);
            // 目录不存在则创建
            if (!is_dir($dir))
                mkdir($dir);
            $_cache[$name] =   $value;
            return file_put_contents($filename, strip_whitespace("<?php\nreturn " . var_export($value, true) . ";\n?>"));
        }
    }
    if (isset($_cache[$name]))
        return $_cache[$name];
    // 获取缓存数据
    if (is_file($filename)) {
        $value = include $filename;
        $_cache[$name] = $value;
    } else {
        $value = false;
    }
    return $value;
}

/**
 * 获取系统配置信息
 * @param string $name 所要获取的配置信息
 * @param string $default 默认值
 * 
 * example：
 * 	GS('base_site_name') 或 GS('base.site_name')效果一致
 * 	GS(null) 删除配置缓存
 */
function GS($name = '', $default=''){
	static $setting = null;
	
	$cache_key = generate_cache_key('global_setting');
	
	if(null === $name){
		S($cache_key, null);
		return;
	}
	
	if($setting === null){
		$setting = S($cache_key);
		if(false === $setting){
			// 不存在，查询配置进行缓存，这里不采用将方法写到模型，函数高内聚，可维护
			$list = D("System")->order('type')->select();
			$cache_list = array();
			if($list && is_array($list)){
				foreach($list as $rs){
					$cache_list[$rs['type'].'_'.$rs['key']] = $rs['value'];
				}
			}
			$setting = $cache_list;
			S($cache_key, $cache_list);
		}
	}
	
	if ($name === '')
		return $setting;
	$name = str_replace('.', '_', $name);
	
	return isset($setting[$name]) ? $setting[$name] : $default;
}

/**
 * 生成md5后的缓存cache_key
 * 
 * 用法：generate_cache_key('product', 1, ...)  // md5(cache_product_1_xxxxxx)
 */
function generate_cache_key(){
	$prefix = C('AUTH_CODE');
	if(empty($prefix)){
		throw_exception('数据缓存设置 AUTH_CODE 选项不能为空，提高memcache安全性');
	}
	
	$args = func_get_args();
	
	array_unshift($args, 'cache');
	array_push($args, $prefix);
	$cache_key = join('_', $args);
	return md5($cache_key);
}

/**
 * 获取基础的缓存对象（单对象）
 * @param string $name 模型名
 * @param integer $primary_id 主键ID值
 * @param integer $expire 缓存有效期（秒）
 */
function cache_get($name, $primary_id, $expire=null){
    $result = null;
    
    $cache_key = generate_cache_key($name, $primary_id);
    $result = S($cache_key);
    if(!$result){
        $result = D($name)->find($primary_id);
        // 默认缓存3600秒
        $expire = is_null($expire) ? 3600 : $expire;
        S($cache_key, $result, $expire);
    }
    return $result;
}


/**
 * 删除缓存对象（单对象）
 */
function cache_rm($name, $primary_id) {
	$cache_key = generate_cache_key($name, $primary_id);
    S($cache_key, null);
}

?>