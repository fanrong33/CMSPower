/*
 +------------------------------------------------------------------------------
 * jQuery validate插件扩展函数库
 +------------------------------------------------------------------------------
 * @author fanrong33
 * @version v1.0.4 Build 20130106
 +------------------------------------------------------------------------------
 */
	
/**
 * 数字
 ******************************/

/** 
 * integer验证
 */
jQuery.validator.addMethod("integer", function(value, element) {   
    var reg = /^[-\+]?\d+$/;
    return this.optional(element) || (reg.test(value));
}, "请输入正确格式的整数");

/** 
 * double验证
 */
jQuery.validator.addMethod("double", function(value, element) {   
    var reg = /^[-\+]?\d+(\.\d+)?$/;
    return this.optional(element) || (reg.test(value));
}, "请输入正确格式的小数");



/**
 * 字符串
 ******************************/

/**
 * 账号
 * 账号以字母开头，6-20位字母、数字、下划线或减号，不与其他用户重复，开通后不能修改
 */
jQuery.validator.addMethod("username", function(value, element) {
	var reg = /^([a-zA-Z]){1}([a-zA-Z0-9_\-]){2,19}$/;
	return this.optional(element) || (reg.test(value));
}, "以字母开头，6-20位字母、数字、下划线或减号");

/**
 * 密码
 * 密码长度6-16个字符，不可以为9位以下纯数字
 */
jQuery.validator.addMethod("nopurepassword", function(value, element) {
	var reg = /^[0-9]{6,8}$/;
	return this.optional(element) || (!reg.test(value));
}, "密码不可以为9位以下纯数字");

/**
 * 手机号码
 */
jQuery.validator.addMethod("mobile", function(value, element) {
	var reg = /^((\(\d{2,3}\))|(\d{3}\-))?(13|15|18)\d{9}$/;
	return this.optional(element) || (reg.test(value));
}, "请输入正确格式的手机号码");

/**
 * 固定电话
 */
jQuery.validator.addMethod("phone", function(value, element) {
	var reg = /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/;
	return this.optional(element) || (reg.test(value));
}, "请输入正确格式的固定电话号码");

/** 
 * 邮政编码验证
 */   
jQuery.validator.addMethod("zipCode", function(value, element) {   
    var reg = /^[0-9]{6}$/;
    return this.optional(element) || (reg.test(value));
}, "请输入正确格式的邮政编码");


/** 
 * 判断字符串是否包含汉字
 */   
jQuery.validator.addMethod("chinese", function(value, element) {
    return this.optional(element) || (escape(value).indexOf("%u") != -1);
}, "请确认字符串中包含中文字符");

/**
 * 中文字两个字节
 */ 
jQuery.validator.addMethod("byteRangeLength", function(value, element, param) {
    var length = value.length;
    for(var i = 0; i < value.length; i++){
        if(value.charCodeAt(i) > 127){
            length++;
        }
    }
  return this.optional(element) || ( length >= param[0] && length <= param[1] );   
}, jQuery.format("请确保输入的值在{0}-{1}个字节之间(一个中文字算2个字节)"));

/**
 * space
 */
jQuery.validator.addMethod("nowhitespace", function(value, element) {
	var reg = /^\S+$/i;
	return this.optional(element) || (reg.test(value));
}, "不可以存在空格(space)");

/**
 * IP地址验证
 */
jQuery.validator.addMethod("ip", function(value, element) {
	var reg = /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/;
	return this.optional(element) || (reg.test(value));
}, "请输入正确格式的IP地址");


/**
 * idcard 身份证号码验证
 */
jQuery.validator.addMethod("idcard", function(value, element) {
	var result = checkIDCard(value);
	var reg = /^\S+$/i;
	return this.optional(element) || result[0];
}, "请输入正确格式的身份证号码");

/**
 * 检查输入的身份证号是否正确
 * @param string id  身份证字符串
 * @return array [true/false, '说明']
 */
function checkIDCard(id){
	var city_map = {11:"北京",12:"天津",13:"河北",14:"山西",15:"內蒙古",21:"辽宁",22:"吉林",23:"黑龙江",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山東",41:"河南",42:"湖北",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州" ,53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"} ;
    var sum = 0;
    var info = "";
    var result = new Array() ;
   
    if(id.length!=15 && id.length!=18){
        result[0] = false;
        result[1] = "身份证号码长度错误";
        return result;
    }
   
    if(id.length==15) {//15位身份证验证
        if (isNaN(id)) {
            result[0] = false;
            result[1] = "身份证号码格式错误";
            return result;
        }
        if(city_map[parseInt(id.substr(0,2))]==null) {
            result[0] = false ;
            result[1] = "非法地区";
            return result ;
        }
		
        var sBirthday="19"+id.substr(6,2)+"-"+Number(id.substr(8,2))+"-"+Number(id.substr(10,2)); 
        var d=new Date(sBirthday.replace(/-/g,"/")) ;
		
        if(sBirthday!=(d.getFullYear()+"-"+ (d.getMonth()+1) + "-" + d.getDate())) {
            result[0] = false ;
            result[1] = "非法生日";
            return result ;
        }
   } else {//18位身份证验证
        if(!/^\d{17}(\d|x)$/i.test(id)) {
            result[0] = false ;
            result[1] = "非身份证号码";
            return result; 
        }
		
        id=id.replace(/x$/i,"a"); 
        if(city_map[parseInt(id.substr(0,2))]==null) {
            result[0] = false ;
            result[1] = "非法地区";
            return result;
        }
		
        var sBirthday=id.substr(6,4)+"-"+Number(id.substr(10,2))+"-"+Number(id.substr(12,2)); 
        var d=new Date(sBirthday.replace(/-/g,"/")) ;
        if(sBirthday!=(d.getFullYear()+"-"+ (d.getMonth()+1) + "-" + d.getDate())) {
            result[0] = false ;
            result[1] = "非法生日";
            return result ;
        }
		
        for(var i = 17;i>=0;i --) sum += (Math.pow(2,i) % 11) * parseInt(id.charAt(17 - i),11) ;
        if(sum%11!=1) {
            result[0] = false ;
            result[1] = "非法证号";
            return result;
        }
    } 
     result[0] = true ;
     result[1] = "合法证件\r\n\r\n证件基本信息为："+city_map[parseInt(id.substr(0,2))]+","+sBirthday+","+(id.substr(16,1)%2?"男":"女") ;      
      
     return result ;
}


/**
 * 时间
 ******************************/

/** 
 * 日期格式验证
 * 验证短日期（2007-06-05）
 */   
jQuery.validator.addMethod("date", function(value, element) {   
    var reg = /^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/;
    return this.optional(element) || (reg.test(value));
}, "请输入正确格式的日期");

/** 
 * 时间格式验证
 * 验证时间（10:57:10）
 */   
jQuery.validator.addMethod("time", function(value, element) {   
    var matches = value.match(/^(\d{1,2})(:)?(\d{1,2})\2(\d{1,2})$/);
	if(matches == null) return false;
	var result = true;
	if(matches[1] > 24 || matches[3] > 60 || matches[4] > 60){
		result = false;
	}else{
		result = true;
	}
    return this.optional(element) || (result);
}, "请输入正确格式的时间");

/** 
 * 全日期时间格式验证
 * 验证（2007-06-05 10:57:10）
 */   
jQuery.validator.addMethod("datetime", function(value, element) {   
    var reg = /^(?:19|20)[0-9][0-9]-(?:(?:0[1-9])|(?:1[0-2]))-(?:(?:[0-2][1-9])|(?:[1-3][0-1])) (?:(?:[0-2][0-3])|(?:[0-1][0-9])):[0-5][0-9]:[0-5][0-9]$/;
    return this.optional(element) || (reg.test(value));
}, "请输入正确格式的全日期时间");