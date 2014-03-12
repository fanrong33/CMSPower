<?php

/**
 +----------------------------------------------------------------------------
 * CMSPower安装程序
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.0 Build 20130713
 +------------------------------------------------------------------------------
 */
define('SITE_DIR', dir_path(substr(dirname(__FILE__), 0, -8)));

if (file_exists('./install.lock')) {
	header('Content-Type: text/html; charset=UTF-8');
	exit('你已经安装过该系统，如果想重新安装，请先删除站点install目录下的 install.lock 文件，然后再安装。');
}

@set_time_limit(1000);
if (phpversion() <= '5.3.0')
    set_magic_quotes_runtime(0);
if (phpversion() < '5.2.0')
    exit('您的php版本过低，不能安装本软件，请升级到5.2.0或更高版本再安装，谢谢！');

date_default_timezone_set('PRC');
error_reporting(E_ALL & ~E_NOTICE);
header('Content-Type: text/html; charset=UTF-8');

include(SITE_DIR . "install/lib/Dir.class.php");
$Dir = new Dir(SITE_DIR);

// 数据库文件
$sql_file = 'cmspower.sql';
$config_file = 'config.php';
$debug_file = 'debug.php';

if (!file_exists(SITE_DIR . 'install/' . $sql_file) || !file_exists(SITE_DIR . 'install/' . $config_file) || !file_exists(SITE_DIR . 'install/' . $debug_file)) {
	exit('缺少必要的安装文件');
}

$title 	 = 'CMSPower 安装向导';

$steps = array(
    '1' => '安装许可协议',
    '2' => '运行环境检测',
    '3' => '安装参数设置',
    '4' => '安装详细过程',
    '5' => '安装完成',
);
$step = isset($_GET['step']) ? $_GET['step'] : 1;

// 获取域名 http://cmspower.com/install/index.php?step=5 => http://cmspower.com/ 
$domain = get_domain();

switch ($step) {

    case '1':	// 安装许可协议
        include_once ("./templates/s1.php");
        exit();

    case '2':	// 运行环境检测

        $php_version 		= @ phpversion();
        $os 				= PHP_OS;
        
        $gd_info 			= function_exists('gd_info') ? gd_info() : array();
        $curl_version		= function_exists('curl_version') ? curl_version() : array();
        $server 			= $_SERVER["SERVER_SOFTWARE"];
        $host 				= (empty($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_HOST"] : $_SERVER["SERVER_ADDR"]);
        $name 				= $_SERVER["SERVER_NAME"];
        $max_execution_time = ini_get('max_execution_time');
        $allow_reference 	= (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $allow_url_fopen 	= (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $safe_mode 			= (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');

        $error = 0;
        if (empty($gd_info['GD Version'])) {
            $gd = '<span class="correct_span error_span">&radic;</span> 未开启';
            $error++;
        } else {
            $gd = '<span class="correct_span">&radic;</span> ' . $gd_info['GD Version'];
        }
        //curl
        if(empty($curl_version['version'])){
        	$curl = '<span class="correct_span error_span">&radic;</span> 未开启';
        	$error++;
        }else{
        	$curl = '<span class="correct_span">&radic;</span> ' . $curl_version['version'];
        }
        //memcache
        if ( !extension_loaded('memcache') ) {
            $memcache = '<span class="correct_span error_span">&radic;</span> 未开启';
        	$error++;
        }else{
        	$memcache = '<span class="correct_span">&radic;</span> 已开启';
        }
        if (function_exists('mysql_connect')) {
            $mysql = '<span class="correct_span">&radic;</span> 已安装';
        } else {
            $mysql = '<span class="correct_span error_span">&radic;</span> 出现错误';
            $error++;
        }
        if (ini_get('file_uploads')) {
            $upload_size = '<span class="correct_span">&radic;</span> ' . ini_get('upload_max_filesize');
        } else {
            $upload_size = '<span class="correct_span error_span">&radic;</span> 禁止上传';
        }
        if (function_exists('session_start')) {
            $session = '<span class="correct_span">&radic;</span> 支持';
        } else {
            $session = '<span class="correct_span error_span">&radic;</span> 不支持';
            $error++;
        }
        
        // 目录权限检查
        $folder_list = array(
//			'/',
            'Admin',
            'Home',
            'uploads'
        );
        include_once ("./templates/s2.php");
        exit();

    case '3': // 安装参数设置
        $parse_url = parse_url($domain);
        // output: Array ( [scheme] => http [host] => cmspower.com [path] => / ) 
        
        // ajax对数据库链接配置进行检查
        if ($_GET['testdbpwd']) {
            $db_host = $_POST['dbHost'] . ':' . $_POST['dbPort'];
            $conn = @mysql_connect($db_host, $_POST['dbUser'], $_POST['dbPwd']);
            if ($conn) {
            	// 同时判断数据库是否存在,存在则返回进行提示
            	if(mysql_select_db($_POST['dbName'], $conn)){
            		ajax_return(null, '存在数据库', 1);
            	}else{ // 不存在
	            	ajax_return(null, '不存在数据库', 1);
            	}
            } else {
            	ajax_return(null, 'error', 0);
            }
        }
        include_once ("./templates/s3.php");
        exit();

    case '4': // 安装详细过程
        if (intval($_GET['install'])) {
            $n = intval($_GET['n']);
            $arr = array();
            
            $dbHost = trim($_POST['dbhost']);
            $dbPort = trim($_POST['dbport']);
            $dbName = trim($_POST['dbname']);
            $dbHost = empty($dbPort) || $dbPort == 3306 ? $dbHost : $dbHost . ':' . $dbPort;
            $dbUser = trim($_POST['dbuser']);
            $dbPwd = trim($_POST['dbpw']);
            $dbPrefix = empty($_POST['dbprefix']) ? 'think_' : trim($_POST['dbprefix']);

            $username = trim($_POST['manager']);
            $password = trim($_POST['manager_pwd']);
            
            $site_name = addslashes(trim($_POST['sitename']));	// 网站名称
            $site_url = trim($_POST['siteurl']);				// 网站域名
            $_site_url = parse_url($site_url);
            $sitefileurl = $_site_url['path'] . "uploads/";		// 附件地址
            $seo_description = trim($_POST['siteinfo']);		// 描述
            $seo_keywords = trim($_POST['sitekeywords']);			// 关键词

            $conn = @ mysql_connect($dbHost, $dbUser, $dbPwd);
            if (!$conn) {
                $arr['msg'] = "连接数据库失败!";
                echo json_encode($arr);
                exit;
            }
            mysql_query("SET NAMES 'utf8'"); //,character_set_client=binary,sql_mode='';
            $version = mysql_get_server_info($conn);
            if ($version < 4.1) {
                $arr['msg'] = '数据库版本太低!';
                echo json_encode($arr);
                exit;
            }

            if (!mysql_select_db($dbName, $conn)) {
                // 创建数据时同时设置编码
                if (!mysql_query("CREATE DATABASE IF NOT EXISTS `" . $dbName . "` DEFAULT CHARACTER SET utf8;", $conn)) {
                    $arr['msg'] = '数据库 ' . $dbName . ' 不存在，也没权限创建新的数据库！';
                    echo json_encode($arr);
                    exit;
                }
                if (empty($n)) {
                    $arr['n'] = 1;
                    $arr['msg'] = "成功创建数据库:{$dbName}<br>";
                    echo json_encode($arr);
                    exit;
                }
                mysql_select_db($dbName, $conn);
            }

            //读取数据文件
            $sqldata = file_get_contents(SITE_DIR . 'install/' . $sql_file);
            $sqlFormat = sql_split($sqldata, $dbPrefix);
            
            
            // 执行SQL语句
            $counts = count($sqlFormat);

            for ($i = $n; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);
                
                if (strstr($sql, 'CREATE TABLE')) {
                    preg_match('/CREATE TABLE `([^ ]*)`/', $sql, $matches);
                    mysql_query("DROP TABLE IF EXISTS `$matches[1]");
                    $ret = mysql_query($sql);
                    if ($ret) {
                        $message = '<li><span class="correct_span">&radic;</span>创建数据表' . $matches[1] . '，完成</li> ';
                    } else {
                        $message = '<li><span class="correct_span error_span">&radic;</span>创建数据表' . $matches[1] . '，失败</li>';
                    }
                    $i++;
                    $arr = array('n' => $i, 'msg' => $message);
                    echo json_encode($arr);
                    exit;
                } else {
                    $ret = mysql_query($sql);
                    $message = '';
                    $arr = array('n' => $i, 'msg' => $message);
                    //echo json_encode($arr); exit;
                }
            }

            if ($i == 999999)
                exit;

            // 更新配置信息
            mysql_query("UPDATE `{$dbPrefix}system` SET `value` = '$site_name' WHERE `key`='site_name'");
            mysql_query("UPDATE `{$dbPrefix}system` SET `value` = '$site_url' WHERE `key`='site_url' ");
            mysql_query("UPDATE `{$dbPrefix}system` SET `value` = '$seo_description' WHERE `key`='site_description'");
            mysql_query("UPDATE `{$dbPrefix}system` SET `value` = '$seo_keywords' WHERE `key`='site_keywords'");

            // 读取配置文件，并替换真实配置数据
            $strConfig = file_get_contents(SITE_DIR . 'install/' . $debug_file);
            $strConfig = str_replace('#DB_HOST#', $dbHost, $strConfig);
            $strConfig = str_replace('#DB_NAME#', $dbName, $strConfig);
            $strConfig = str_replace('#DB_USER#', $dbUser, $strConfig);
            $strConfig = str_replace('#DB_PWD#', $dbPwd, $strConfig);
            $strConfig = str_replace('#DB_PORT#', $dbPort, $strConfig);
            $strConfig = str_replace('#DB_PREFIX#', $dbPrefix, $strConfig);
            @file_put_contents(SITE_DIR . 'Admin/Conf/debug.php', $strConfig);
            @file_put_contents(SITE_DIR . 'Home/Conf/debug.php', $strConfig);
            
            
            $strConfig = file_get_contents(SITE_DIR . 'install/' . $config_file);
            $strConfig = str_replace('#DB_HOST#', $dbHost, $strConfig);
            $strConfig = str_replace('#DB_NAME#', $dbName, $strConfig);
            $strConfig = str_replace('#DB_USER#', $dbUser, $strConfig);
            $strConfig = str_replace('#DB_PWD#', $dbPwd, $strConfig);
            $strConfig = str_replace('#DB_PORT#', $dbPort, $strConfig);
            $strConfig = str_replace('#DB_PREFIX#', $dbPrefix, $strConfig);
            $authcode = randString(8, 0);
            $strConfig = str_replace('#AUTHCODE#', $authcode, $strConfig);
            @file_put_contents(SITE_DIR . 'Admin/Conf/config.php', $strConfig);
            @file_put_contents(SITE_DIR . 'Home/Conf/config.php', $strConfig);
            
            // 插入管理员
            $time = time();
            $password = encrypt_pwd($password);
            $query = "INSERT INTO `{$dbPrefix}admin` VALUES ('1', '{$username}', '{$password}', '', 1, '', 0, 0, '0', {$time});";
            
            mysql_query($query);

            $message = '成功添加管理员<br />成功写入配置文件<br>安装完成.';
            $arr = array('n' => 999999, 'msg' => $message);
            echo json_encode($arr);
            exit;
        }

        include_once ("./templates/s4.php");
        exit();

    case '5':
        include_once ("./templates/s5.php");
        @touch('./install.lock');
        exit();
}


function sql_execute($sql, $tablepre) {
    $sqls = sql_split($sql, $tablepre);
    if (is_array($sqls)) {
        foreach ($sqls as $sql) {
            if (trim($sql) != '') {
                mysql_query($sql);
            }
        }
    } else {
        mysql_query($sqls);
    }
    return true;
}


function sql_split($sql, $tablepre) {

    if ($tablepre != "cmspower_")
        $sql = str_replace("cmspower_", $tablepre, $sql);
    $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);

    if ($r_tablepre != $s_tablepre)
        $sql = str_replace($s_tablepre, $r_tablepre, $sql);
    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    $queriesarray = explode(";\n", trim($sql));
    unset($sql);
    foreach ($queriesarray as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        $queries = array_filter($queries);
        foreach ($queries as $query) {
            $str1 = substr($query, 0, 1);
            if ($str1 != '#' && $str1 != '-')
                $ret[$num] .= $query;
        }
        $num++;
    }
    return $ret;
}


function ajax_return($data, $info='', $status=1){
	$result = array();
	$result['data'] = $data;
	$result['info'] = $info;
	$result['status'] = $status;
    // 返回JSON数据格式到客户端 包含状态信息
    header('Content-Type:text/html; charset=utf-8');
	exit(json_encode($result));
}


/**
 * 检查目录文件权限
 */
function dir_create($path) {
    global $Dir;
    if (!$path) {
        return false;
    }
    if (is_dir($path)) {
        $Dir->listFile($path);
        $dir = $Dir->toArray();
        $dir = $dir[0];
    } else {
        $dir['isReadable'] = is_readable($path);
        $dir['isWritable'] = is_writable($path);
    }
    return $dir;
}


/**
 * 将目录路径转换为斜杠的，并且以/结尾
 */
function dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}


/**
 * 获取域名地址
 * http://cmspower.com/
 */
function get_domain(){
	$script_name = !empty($_SERVER["REQUEST_URI"]) ? $script_name = $_SERVER["REQUEST_URI"] : $script_name = $_SERVER["PHP_SELF"];
	$rootpath = @preg_replace("/\/(I|i)nstall\/index\.php(.*)$/", "/", $script_name);
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$domain = empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
	if ((int) $_SERVER['SERVER_PORT'] != 80) {
	    $domain .= ":" . $_SERVER['SERVER_PORT'];
	}
	$domain = $sys_protocal . $domain . $rootpath;
	return $domain;
}


/**
 * 密码加密方法
 */
function encrypt_pwd($password){
    $password = md5($password);
    return md5(crypt($password, substr($password, 0, 2)));
}

/**
 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function randString($len=6,$type='',$addChars='') {
	$str ='';
	switch($type) {
		case 0:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 1:
			$chars= str_repeat('0123456789',3);
			break;
		case 2:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
			break;
		case 3:
			$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 4:
			$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
			break;
	}
	if($len>10 ) {//位数过长重复字符串一定次数
		$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
	}
	if($type!=4) {
		$chars   =   str_shuffle($chars);
		$str     =   substr($chars,0,$len);
	}else{
		// 中文随机字
		for($i=0;$i<$len;$i++){
		  $str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1,'utf-8',false);
		}
	}
	return $str;
}
?>