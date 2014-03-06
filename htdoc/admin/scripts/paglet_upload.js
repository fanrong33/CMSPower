/**
 +----------------------------------------------------------------------------
 * paglet_upload页面js函数
 +----------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.1 Build 20140210
 +------------------------------------------------------------------------------
 */

$(document).ready(function(){
	
	// 绑定tab事件
	$("#J_upload_tabs").tabs();
	
	// 不允许混合选择 新上传 和 图库 中的图片
	$("#J_upload_tabs ul a").click(function(){
		
		$("#cmspower_upload_dialog_confirm").text('确定');
		
		// 初始化选中的样式
		if($(this).attr('href') == '#upload_1'){
			$("#upload_1 #cmspower_uploaded_image li.selected").removeClass('selected');
		}else{
			$("#upload_2 table tbody tr.selected").removeClass('selected');
		}
	});
	
	
	// 选择已上传文件list，添加selected样式
	$("#upload_1 .uploaded-list li").die('click').live('click', function(){
		var $this = $(this);
		$this.toggleClass('selected');
		
		// 获取选中文件的uploaded_url、file_id、file_extension、file_name、file_size
		var uploaded_url 	= $this.data('uploaded-url');
		var file_id 		= $this.data('file-id');
		var file_extension 	= $this.data('file-extension');
		var file_name 		= $this.data('file-name');
		var file_size 		= $this.data('file-size');
		
		if($this.hasClass('selected')){
			//   如果最多上传 1 个附件，则选中前先清空
			if(!MULTI){
				$this.siblings('.selected').removeClass('selected');
				CMSPOWER_SELECTED = new Array();
			}
			
			// 选中
			CMSPOWER_SELECTED[file_id] = {
				'uploaded_url'	: uploaded_url, 
				'file_id'		: file_id,
				'file_name' 	: file_name,
				'file_extension': file_extension,
				'file_size'		: file_size
			};
		}else{
			// 取消选中
			delete CMSPOWER_SELECTED[file_id];
		}
		
		var count = 0;
		for(var i in CMSPOWER_SELECTED){
			count++;
		}
		
		// 3、确定按钮显示选中的数量
		if(count == 0){
			confirm_text = '确定';
		}else{
			confirm_text = '确定('+count+')';
		}
		$("#cmspower_upload_dialog_confirm").text(confirm_text);
		
	});
	
	
	// 选择已上传图片，添加selected样式
	$("#upload_1 .uploaded-preview li").die('click').live('click', function(){
		var $this = $(this);
		$this.toggleClass('selected');
		
		// 获取选中图片的uploaded_url、file_id、width、height
		var uploaded_url 	= $this.data('uploaded-url');
		var file_id 		= $this.data('file-id');
		var file_name 		= $this.data('file-name');
		var width			= $this.data('width');
		var height			= $this.data('height');
		
		if($this.hasClass('selected')){
			// 如果最多上传 1 个附件，则选中前先清空
			if(!MULTI){
				$this.siblings('.selected').removeClass('selected');
				CMSPOWER_SELECTED = new Array();
			}
			
			// 选中
			CMSPOWER_SELECTED[file_id] = {
				'uploaded_url'	: uploaded_url,
				'file_id'		: file_id,
				'file_name'		: file_name,
				'width'			: width,
				'height'		: height
			};
		}else{
			// 取消选中
			delete CMSPOWER_SELECTED[file_id];
		}
		
		var count = 0;
		for(var i in CMSPOWER_SELECTED){
			count++;
		}
		
		// 3、确定按钮显示选中的数量
		if(count == 0){
			confirm_text = '确定';
		}else{
			confirm_text = '确定('+count+')';
		}
		$("#cmspower_upload_dialog_confirm").text(confirm_text);
	});
	
	// 选择图库中的图片，添加selected样式
	$("#upload_2 a.img-preview").die('click').live('click', function(event){
		event.preventDefault ? event.preventDefault() : event.returnValue = false;
		var $this = $(this);
		$this.parent().parent().toggleClass('selected');
		
		// 获取选中图片的image_url和file_id
		var image_url = $this.attr('href').substr(1);
		var file_id = $this.data('file-id');
		
		
		var file_id_str = $("#cmspower_selected_file_id").text();
		var file_id_list = file_id_str.split("|");
		
		var image_url_str = $("#cmspower_selected_image_url").text();
		var image_url_list = image_url_str.split("|");
		
		
		if($this.parent().parent().hasClass('selected')){
			// 选中, 循环遍历#cmspower_selected_image_url，不存在则添加
			
			//   如果最多上传 1 个附件，则选中前先清空
			if(!MULTI){
				$this.parent().parent().siblings('.selected').removeClass('selected');
				image_url_list = new Array;
				file_id_list = new Array;
			}
			
			var has_selected = false;
			for(var i in file_id_list){
				if (file_id == file_id_list[i]) {
					has_selected = true;
				}
			}
			if(!has_selected){
				image_url_list.push(image_url);
				file_id_list.push(file_id+"");
			}
		}else{
			// 取消选中，循环遍历#cmspower_selected_image_url，存在则删除
			var index = -1;
			for(var i in file_id_list){
				if(file_id == file_id_list[i]){
					index = i;
					break;
				}
			}
			if(index != -1){
				image_url_list.splice(index, 1);
				file_id_list.splice(index, 1);
			}
		}
		
		// 过滤image_url_list和file_id_list为空的对象
		var index = -1;
		for(var i in file_id_list){
			if(file_id_list[i] == ""){
				index = i;
				break;
			}
		}
		if(index != -1){
			image_url_list.splice(index, 1);
			file_id_list.splice(index, 1);
		}
		
		// 2、选中图片将属性设置到隐藏域中，使“上传图片”和“图库”选择图片通用
		$("#cmspower_selected_image_url").text(image_url_list.join('|'));
		$("#cmspower_selected_file_id").text(file_id_list.join('|'));
		
		// 3、确定按钮显示选中的数量
		if(file_id_list.length == 0){
			confirm_text = '确定';
		}else{
			confirm_text = '确定('+file_id_list.length+')';
		}
		$("#cmspower_upload_dialog_confirm").text(confirm_text);
	});
	
})

/**
 * 确定，将上传的文件信息填到表单中
 * @param {Object} object
 */
function confirm(object){
	// 若没有选中任何文件，则直接关闭
	var count = 0;
	for(var i in CMSPOWER_SELECTED){
		count++;
	}
	if(count == 0){
		var $this = $(object);
		$this.closest('.dialog').find('div[data-role=close]').trigger('click');
		return;
	}
	
	if(!MULTI){
		// 若为单文件上传，先清空
		$("#cmspower_uploaded_"+FIELD+" ul").empty();
	}
	
		
	var data = {};
	for(var i in CMSPOWER_SELECTED){
		data = CMSPOWER_SELECTED[i];
			
		// 文件列表模式
		if (MODE == 'list') {
			if(MULTI){
				var tpl = 
				'<li>'+
				'	<input type="hidden" name="'+FIELD+'[]" value="<%= uploaded_url %>">'+
				'	<input type="hidden" name="'+FIELD+'_file_id[]" value="<%= file_id %>">'+
				'	<i class="icon-file-<%= file_extension %> mr5"></i>'+
				'	<span><%= file_name %></span>'+
				'	<span class="txt-gray">(<%= file_size %>)</span>'+
				'	<a class="delete" href="javascript:;" onclick="delete_attachment(this);"><i class="iconfont">&#x49;</i></a>'+
				'</li>';
			}else{
				var tpl = 
				'<li>'+
				'	<input type="hidden" name="'+FIELD+'" value="<%= uploaded_url %>">'+
				'	<input type="hidden" name="'+FIELD+'_file_id" value="<%= file_id %>">'+
				'	<i class="icon-file-<%= file_extension %> mr5"></i>'+
				'	<span><%= file_name %></span>'+
				'	<span class="txt-gray">(<%= file_size %>)</span>'+
				'	<a class="delete" href="javascript:;" onclick="delete_attachment(this);"><i class="iconfont">&#x49;</i></a>'+
				'</li>';
			}
		}
		
		// 预览图片模式
		if(MODE == 'preview'){
			if(MULTI){
				var tpl = 
				'<li>'+
				'	<input type="hidden" name="'+FIELD+'[]" value="<%= uploaded_url %>">'+
				'	<input type="hidden" name="'+FIELD+'_file_id[]" value="<%= file_id %>">'+
				'	<div class="preview">'+
				'		<img <% if(width>height) {%>width="100"<% }else{ %>height="100"<% } %> src="/<%= uploaded_url %>">' +
				'	</div>'+
				'	<input type="text" value="<%= file_name %>">'+
				'	<a class="delete" href="javascript:;" onclick="delete_attachment(this);"><i class="iconfont">&#x49;</i></a>'+
				'</li>';
			}else{
				var tpl = 
				'<li>'+
				'	<input type="hidden" name="'+FIELD+'" value="<%= uploaded_url %>">'+
				'	<input type="hidden" name="'+FIELD+'_file_id" value="<%= file_id %>">'+
				'	<div class="preview">'+
				'		<img <% if(width>height) {%>width="100"<% }else{ %>height="100"<% } %> src="/<%= uploaded_url %>">' +
				'	</div>'+
				'	<input type="text" value="<%= file_name %>">'+
				'	<a class="delete" href="javascript:;" onclick="delete_attachment(this);"><i class="iconfont">&#x49;</i></a>'+
				'</li>';
			}
		}
		
		var render = template.compile(tpl);
		var html = render(data);
		
		$("#cmspower_uploaded_"+FIELD+" ul").append(html);
	}
	
	var $this = $(object);
	$this.closest('.dialog').find('div[data-role=close]').trigger('click');
}

/**
 * 关闭对话框
 * @param {Object} object
 */
function cancel(object){
	var $this = $(object);
	$this.closest('.dialog').find('div[data-role=close]').trigger('click');
}