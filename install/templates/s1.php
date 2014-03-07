<!doctype html>
<html>
<head>
	<meta charset="UTF-8" />
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" href="./css/install.css" />
	<script type="text/javascript" src="./js/jquery.js"></script>
	<script>
	$(function(){
		setTimeout(function(){
			$(".wrap").addClass('wrap-active');
		}, 300);
	});	
	</script>
</head>
<body>
	<div class="wrap">
  		<?php require './templates/header.php';?>
		<div class="section">
			<div class="main cc">
      			<pre class="pact" readonly="readonly"><strong>CMSPower 软件使用协议</strong>

版权所有(c)2012-2013，cmspower.cn保留所有权力。

感谢您选择 CMSPower 建站系统, 希望我们的产品能够帮您把网站发展的更快、更好、更强！

CMSPower 建站系统由cmspower.cn(以下简称本公司或CMSPower官方)独立开发，全部核心技术归属 cmspower.cn。
官方网站为 http://www.cmspower.cn

本授权协议适用于 CMSPower 任何版本，本公司拥有对本授权协议的最终解释权和修改权。

CMSPower 建站系统使用限制 
  1、您在使用 CMSPower 时应遵守中华人民共和国相关法律法规、您所在国家或地区之法令及相关国际惯例，不将 CMSPower 用于任何非法目的，也不以任何非法方式使用 CMSPower。
  2、如果您需要采用 CMSPower 系统的部分程序构架其他程序系统，请务必取得我们的同意。否则我们将追究责任!修改后的代码，未经书面许可，严禁公开发布，更不得利用其从事盈利业务。
  3、所有用户均可查看 CMSPower 的全部源代码,也可以根据自己的需要对其进行修改！但无论如何，既无论用途如何、是否经过修改或美化、修改程度如何，只要您使用 CMSPower 的任何整体或部分程序算法，都必须保留页脚处的网站(http://www.cmspower.cn)链接地址，不能清除或修改。
  4、未经商业授权,不得将本软件用于商业用途(企业网站或以盈利为目的经营性网站)，否则我们将保留追究的权力。 

CMSPower 免责声明
  1、利用 CMSPower 构建的网站的任何信息内容以及导致的任何版权纠纷和法律争议及后果，CMSPower 官方不承担任何责任。
  2、CMSPower 损坏包括程序的使用(或无法再使用)中所有一般化、特殊化、偶然性的或必然性的损坏(包括但不限于数据的丢失，自己或第三方所维护数据的不正确修改，和其他程序协作过程中程序的崩溃等)，CMSPower 官方不承担任何责任。

电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦安装使用CMSPower，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，本公司有权随时终止授权，责令停止损害，并保留追究相关责任的权力。

</pre>
    		</div>
    		<div class="bottom tac"><a href="./index.php?step=2" class="btn">接 受</a></div>
  		</div>
	</div>
	<?php require './templates/footer.php';?>
</body>
</html>