<include file="Public:header" />
	
	<div class="container">
		<?php
			if($message){
				$message = $message;
				$class = 'success';
				$iconfont = '&#x24;';
			}else{
				$message = $error;
				$class = 'error';
				$iconfont = '&#x22;';
			}
		?>
		<div style="padding: 50px 10px 150px 270px;">
			<div class="tipbox tipbox-white tipbox-<?php echo $class; ?>">
			    <div class="tipbox-icon">
			        <i class="iconfont"><?php echo $iconfont; ?></i>
			    </div>
			    <div class="tipbox-content">
			        <h3 class="tipbox-title"><?php echo $message; ?></h3>
			        <p class="tipbox-explain">页面正在为您自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间：<b id="wait"><?php echo($waitSecond); ?></b><?php if($waitSecond >= 100){ ?><b>ms</b><?php } ?></p>
			    </div>
			</div>
		</div>
	
	</div><!-- .container -->
	
	<script type="text/javascript">
	(function(){
		var wait = document.getElementById('wait'),href = document.getElementById('href').href;
		<?php if($waitSecond < 100){ ?>
			var interval = setInterval(function(){
				var time = --wait.innerHTML;
				if(time == 0) {
					location.href = href;
					clearInterval(interval);
				};
			}, 1000);
		<?php }else{?>
			var interval = setInterval(function(){
				location.href = href;
				clearInterval(interval);
			}, wait.innerHTML);
		<?php } ?>
	})();
	</script>

<include file="Public:footer" />