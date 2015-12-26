//#########################################################################################################
//#										全局函数 变量
//#########################################################################################################


var JS_ISDEBUG = true;




/*
	获取项目跟路径
*/
function getRootPath(){
    var curWwwPath=window.document.location.href;
    var pathName=window.document.location.pathname;
    var pos=curWwwPath.indexOf(pathName);
    var localhostPaht=curWwwPath.substring(0,pos);
    var projectName=pathName.substring(0,pathName.substr(1).indexOf('/')+1);
    return(localhostPaht+projectName);
}

/*删除字符串左右两端的空格*/
function trim(str){ 
	if(str=="") return str;
　　return str.replace(/(^\s*)|(\s*$)/g, "");
}


/*弹框显示*/
function alertMsg(msg, exMsg){
	if(JS_ISDEBUG){
		$("#msg-tip").html(msg + exMsg).show(300).delay(1000).hide(300);
	}
	else{
		$("#msg-tip").html(msg).show(300).delay(1000).hide(300);
	}
}

/*提示框显示*/
function showTip(msg, time){
	$("#msg-tip").html(msg).show(300).delay(time).hide(300);
}