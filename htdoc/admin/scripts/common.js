/**
 +----------------------------------------------------------------------------
 * js公共函数库
 +----------------------------------------------------------------------------
 * @category 后台应用
 * @author fanrong33 <fanrong33#qq.com>
 * @version v1.1.1 Build 20140307
 +------------------------------------------------------------------------------
 */
$(document).ready(function(){
	
	// 所有的ajax form提交，由于大多业务逻辑都是一样的，故统一处理
	if($('.J_ajax_submit_btn').length){
		$(".J_ajax_submit_btn").click(function(event){
			event.preventDefault ? event.preventDefault() : event.returnValue = false;
			
			var $this 		= $(this);
			var $form 		= $this.closest('form');
			var action_url 	= $this.data('action');
			var check 		= $this.data('check');
			check = (check == "undefined") ? 0 : check; // 没有默认为不需要check
			
			// 当“批量删除”等操作，进行判断是否勾选checkbox
			if(check == 1){
				if ($form.find('tbody').find('input:checked').length == 0){
					// alert('您没有勾选信息');
					$.notify('未选中任何信息', 'warn');
					return;
				}
			}
			
			$form.ajaxSubmit({
				url: action_url,
        		dataType: "json",
	            success: function(json){
	                
	                if(json.status==1){
						$.notify(json.info);
	                    window.location.href=window.location.href;
	                }else{
						$.notify(json.info, 'warn');
	                }
	            }
	        }); 
		});
	}
	
	// 绑定ajax提交事件，为form表单添加 钩子样式 J_ajax_submit
	$("form.J_ajax_submit").ajaxForm({
		dataType: "json",
        beforeSubmit: function(){
			$.notify("正在提交...");
			return true;
        },
        success: function(json){
			if(json.status==1){
                $.notify(json.info, 'success');
				window.location.href=json.url;
			}else{
                $.notify(json.info, 'warn');
			}
        }
	});
	
	// 为表格增加hover
	$("table tbody tr").hover(function(){
		$(this).addClass('table-hover');
	}, function(){
		$(this).removeClass('table-hover');
	});
	
	// 图片预览
	if($('a.img-preview').length > 0){
		$('a.img-preview').preview({
		    onShow: function(link){
				var image_size = get_image_size($(link).attr('href'));
		        $('<span>' + image_size.width + ' x ' + image_size.height + '</span>').appendTo(this);
		    },
		    onHide: function(link){
		        $('span', this).remove();
		    }
		});
	}
	
	// 绑定表单checkbox事件，添加选中背景颜色
	if($('form').length){
		$('table tbody input:checkbox').click(function(){
			var $this = $(this);
			var $table = $(this).closest('table');
			if($this.attr('checked')){
				$this.closest('tr').addClass('table-selected');
				
				// 联动全选check_all
				var checked_count = $table.find('tbody input:checked').length;
				var checkbox_total = $table.find('tbody input:checkbox').length;
				if (checked_count == checkbox_total) {
					$table.find('thead input:checkbox').attr('checked', true);
				}
			}else{
				$this.closest('tr').removeClass('table-selected');
				
				// 联动取消全选check_all
				$table.find('thead input:checkbox').attr('checked', false);
			}
		});
	}
	
	// 鼠标移动到上传图片li上显示删除图标
	$(".uploaded-preview li").live('mouseover', function(){
		$(this).addClass('hover');
	}).live('mouseout', function(){
		$(this).removeClass('hover');
	});
	$(".uploaded-list li").live('mouseover', function(){
		$(this).addClass('hover');
	}).live('mouseout', function(){
		$(this).removeClass('hover');
	});
	
	// tab面板切换
	$("#tabs").tabs();
	
	// 侧边栏固定
	$("#aside").fixed();
	
	// 分页跳转input获取焦点，默认选中文本
	$("div.paging input").focus(function(){
		try {
			$(this).get(0).select();
		} catch (e) {
		}
	});
})

/////////////////////////////////////////////////////////////////////////////////////////////

// 固定采用一种方式，否则会造成混乱，采用onclick方式产生交互

/**
 * 过滤条件
 * @param {Object} object
 * @example onchange="filter_condition(this)"
 */
function filter_condition(object){
	var $this = $(object);
	
	var params = new Array();
	for(var i=1; i<arguments.length; i=i+2){
		params.push(arguments[i] + '=' + arguments[i+1]);
	}
	
	// 默认获取本身的条件
	var key = $this.attr('name');
	var id = $this.val();
	params.push(key + '=' + id);
	var search = "?" + params.join('&');
	
	//window.location.protocol + window.location.host + window.location.pathname
	window.location.href = window.location.pathname + search; // 必须是pathinfo模式
	//window.location.href=PHP_FILE+"?m=url&a=index&is_show="+is_show+"&ad_position_id="+id;  X(out)
}


/**
 * 获取图片尺寸
 * @param {Object} FilePath
 */
function get_image_size(file_path){
    var imagesize={
	    width:0,
	    height:0
    };
	
    image = new Image();
    image.src = file_path;
    imagesize.width = image.width;
    imagesize.height= image.height;
    return imagesize;
}

/**
 * ajax更新切换enum类型字段值，is_xxx
 * @param {Object} object DOM对象
 * @param {Object} table 更改模型表名
 * @param {Object} field 字段
 * @param {Object} value 新的值
 * 
 */
function toggle_field(object){
	var $this = $(object);
	
	var params = {};
	params['table'] = $this.data('table');
	params['field'] = $this.data('field');
	params['id'] 	= $this.data('id');
	params['value'] = $this.data('value');
	
	$.ajax({
		type:"POST",
		url: "/admin.php/util/toggle_field",
		data: params,
		dataType: "json",
		success:function(json){
			if(json.status==1){
				$.notify(json.info, 'success');
				
				var iconfont = params['value'] == 1 ? $this.data('open-iconfont') : $this.data('close-iconfont');
				$this.find("i").text(iconfont);
				
				// 切换1/0
				$this.data('value', params['value'] == 1 ? 0 : 1);
			}else{
				$.notify(json.info, 'warn');
				//alert(json.info);
			}
		}
	});
}

/**
 * 全选/取消复选框
 * @param {Object} string 	element	包裹复选框的的容器节点，id 或 class 选择器
 * @param {Object} boolean 	force	强制选中或取消
 */
function check_all(element, force){
	if(typeof force == 'undefined'){
		var checked_count = $(element).find('input:checked:enabled').length;
		var checkbox_total = $(element).find('input:checkbox:enabled').length;
		
		if(checked_count < checkbox_total){
			$(element).find('input:checkbox:enabled').each(function(){
				$(this).attr('checked', true);
				$(this).closest('tr').addClass('table-selected');
			});
		}else{
			$(element).find('input:checkbox:enabled').each(function(){
				$(this).attr('checked', false);
				$(this).closest('tr').removeClass('table-selected');
			});
		}
		
		var $table = $(element).closest('table');
		if(checked_count < checkbox_total){
			// 联动全选check_all
			$table.find('thead input:checkbox:enabled').attr('checked', true);
		}else{
			// 联动取消全选check_all
			$table.find('thead input:checkbox:enabled').attr('checked', false);
		}
	}else{
		$(element).find('input:checkbox:enabled').each(function(){
			$(this).attr('checked', force);
			if(force){
				$(this).closest('tr').addClass('table-selected');
			}else{
				$(this).closest('tr').removeClass('table-selected');
			}
		});
	}
}


/**
 * 推送
 * 推送内容必须为内容模块
 * @param string ch 产品所在的栏目
 */
function push(ch){
	// 判断是否有选择checkbox
	var checked_count = $('table tbody').find('input:checked').length;
	
	if(checked_count == 0){
		//alert('您没有勾选信息');
		$.notify('未选中任何信息', 'warn');
		return false;
	}
	
	$.dialog.show({
		width: 550,
		remote:{
	        url: PHP_FILE+"?m=util&a=paglet_push&ch="+ch,
	        success: function($object){
				console.info($object);
				
				// 将选中的表单ids[]写入隐藏域
				var input_array = $('table tbody').find('input:checked').serializeArray();
				var count = input_array.length;
				
				var html = '';
				for(var i=0; i<count; i++){
					var input = input_array[i];
					html = '<input type="hidden" name="'+input.name+'" value="'+input.value+'">';
					$object.find('form').append(html);
				}
	        }
	    }
	});
}


/**
 * 往容器追加内容，并且激活光标
 * @param {Object} obj 容器DOM
 * @param {Object} str 插入的内容
 * insert_text(document.getElementById('content'), text);
 */
function insert_text(obj, str) {
    obj.focus();
    if (document.selection) {
        var sel = document.selection.createRange();
        sel.text = str;
    } else if (typeof obj.selectionStart === 'number' && typeof obj.selectionEnd === 'number') {
        var startPos = obj.selectionStart,
            endPos = obj.selectionEnd,
            cursorPos = startPos,
            tmpStr = obj.value;
        obj.value = tmpStr.substring(0, startPos) + str + tmpStr.substring(endPos, tmpStr.length);
        cursorPos += str.length;
        obj.selectionStart = obj.selectionEnd = cursorPos;
    } else {
        obj.value += str;
    }
}

/**
 * ajax获取上一个select的选择的选项值
 * @param {Object} trigger		触点元素节点，仅支持一个 id 或 class 选择器
 * @param {Object} model_name	模型名
 * @param {Object} field		要获取的上一个的字段
 */
function get_last_selected(trigger, model_name, field){
	// alert('点击自动获取上一个的');
	if($(trigger).length==0){
		alert('get_last_selected(trigger, model_name, field), 未设置trigger触点元素节点');
		return;
	}
	
	$.ajax({
		type: "GET",
		url: PHP_FILE+'?m=util&a=get_last_selected',
		data: {model_name: model_name, field: field},
		dataType: 'json',
		beforeSend: function(){
			$.notify("正在提交...");
		},
		success: function(json){
			if(json.status==1){
				$.notify(json.info, 'success');
				
				$(trigger).setSelectedValue(json.data);
				$(trigger).trigger('change');
			}else{
				$.notify(json.info, 'warn');
			}
		}
	})
}

/**
 +----------------------------------------------------------------------------
 * 上传附件控件相关函数
 +------------------------------------------------------------------------------
 */


/**
 * 删除已上传图片隐藏域
 * @param object this
 */
function delete_attachment(object){
	var $this = $(object);
	$this.closest('li').fadeOut(function(){
		$(this).remove();
	});
}

/**
 * 显示上传附件对话框
 * 
 * @param string field	文件对应存储的字段
 * @param string dir 	上传目录(*加密后)
 * @param boolean multi 是否支持多文件上传
 * @param string mode 	模式，list-文件列表，preview-图片预览
 * @param string exts 	支持的文件后缀（*加密后）
 */
function show_upload_dialog(field, dir, multi, mode, exts){
	$.dialog.show({
		width: 650,
        remote:{
	        url: PHP_FILE+'?m=util&a=paglet_upload',
			data: {field:field, dir: dir, multi: multi, mode: mode, exts: exts}
	    }
    });
}
