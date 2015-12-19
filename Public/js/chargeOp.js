
/*退出管理登录*/
function logoutCharge(){
	$.ajax({ url: getRootPath() + "/Charge/logoutCharge", 
	 	success: function(responseData){
	 		if(responseData == "true"){
	 			window.location.href="Charge/login";
	 		}
	    },
	    error: function(){
	    	alertMsg("出错了","");
	    }
  	});
}



//#########################################################################################################
//#										用户操作
//#########################################################################################################

/*
	删除用户 TODO
*/
function deleteUser(obj, delUserId){
	if (confirm("确认要删除？")) {
		var delId = {
			userId: delUserId,
		};
        $.ajax({ url: getRootPath() + "/Charge/deleteUser", 
        	data: delId,
		 	context: obj.parentNode.parentNode.parentNode,
		 	success: function(responseData){
		 		if(responseData == "true"){
		        	this.removeChild(obj.parentNode.parentNode);
		 		}
		 		else{
		 			alertMsg("删除失败",responseData);
		 		}
		    },
		    error: function(){
		    	alertMsg("出错了","");
		    }
	  	});
    }
}

/*
	通知用户
*/
function notifyUser(obj, toUserId){
	var message=prompt("输入通知内容","");
	if (message!=null && message!=""){
		var msgData = {
			userId: toUserId,
			content: message,
		};
		$.ajax({ url: getRootPath() + "/Charge/notifyUser", 
        	data: msgData,
        	type: 'POST',
		 	context: obj,
		 	success: function(responseData){
		 		if(responseData == "true"){
		        	//Do nothing
		 		}
		 		else{
		 			alertMsg("通知失败",responseData);
		 		}
		    },
		    error: function(){
		    	alert("出错了");
		    }
	  	});
	}
}


//#########################################################################################################
//#										文章禁止 删除
//#########################################################################################################

/*删除文章*/
function deleteArticle(obj, delUrl){
    if (confirm("确认要删除？")) {
        $.ajax({ url: delUrl, 
		 	context: obj.parentNode.parentNode.parentNode.parentNode.parentNode,
		 	success: function(responseData){
		 		if(responseData == "true"){
		        	this.parentNode.removeChild(this);
		 		}
		 		else{
		 			alertMsg("删除失败",responseData);
		 		}
		    },
		    error: function(){
		    	alertMsg("出错了","");
		    }
	  	});
    }
}

/*审核禁止或解禁文章*/
function banArticle(obj, artId){
	var ban = $(obj).attr('ban');
	if(ban == 1){
		if (confirm("确认要禁止？")) {
	    	var banData = {
	    		articleId: artId,
	    		isBan: 1,
	    	};
	        $.ajax({ url: getRootPath() + "/Charge/banArticle", 
	        	data : banData,
			 	context: obj,
			 	success: function(responseData){
			 		if(responseData == "true"){
			        	$(this).html("<span class=\"icon-check text-red\"></span> 解禁");
			        	$(this).attr('class', 'button bg-green');
			 			$(obj).attr('ban', '0');
			 		}
			 		else{
			 			alertMsg("禁止失败",responseData);
			 		}
			    },
			    error: function(){
			    	alertMsg("出错了","");
			    }
		  	});
	    }
	}
	else{
		if (confirm("确认要解除禁止？")) {
	    	var banData = {
	    		articleId: artId,
	    		isBan: 0,
	    	};
	        $.ajax({ url: getRootPath() + "/Charge/banArticle", 
	        	data : banData,
			 	context: obj,
			 	success: function(responseData){
			 		if(responseData == "true"){
			 			$(this).html("<span class=\"icon-ban text-red\"></span> 禁止");
			 			$(this).attr('class', 'button bg-green-light');
			 			$(obj).attr('ban', '1');
			 		}
			 		else{
			 			alertMsg("解禁失败",responseData);
			 		}
			    },
			    error: function(){
			    	alertMsg("出错了","");
			    }
		  	});
	    }
	}
    
}
