<!doctype html>
<html>
<head>
	<meta charset="UTF-8" />
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" href="./css/install.css?v=9.0" />
</head>
<body>
	<div class="wrap wrap-active">
	  	<?php require './templates/header.php';?>
	  	<section class="section">
	    	<div class="">
	      		<div class="success_tip cc"> <a href="<?php echo $domain ?>admin.php" class="f16 b">安装完成，点击进入后台管理</a>
	        		<p></p>
					<p>进入后台以后，第一件事是<font color="#FF0000">更新网站缓存</font>，不然有些功能不正常！<p>
					<p>为了您站点的安全，安装完成后即可将网站根目录下的“install”文件夹删除。<p>
	      		</div>
	      		<div class=""> </div>
	    	</div>
	  	</section>
	</div>
	<?php require './templates/footer.php';?>
</body>
</html>