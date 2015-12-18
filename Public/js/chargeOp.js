function logoutCharge(){
	$.ajax({ url: "Charge/logoutCharge", 
	 	success: function(responseData){
	 		if(responseData == "true"){
	 			window.location.href="Charge/login";
	 		}
	    },
	    error: function(){
	    	alert("出错了");
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
        $.ajax({ url: "Charge/deleteUser", 
        	data: delId,
		 	context: obj.parentNode.parentNode.parentNode,
		 	success: function(responseData){
		 		if(responseData == "true"){
		        	this.removeChild(obj.parentNode.parentNode);
		 		}
		 		else{
		 			alert("删除失败" + responseData);
		 		}
		    },
		    error: function(){
		    	alert("出错了");
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
		$.ajax({ url: "Charge/notifyUser", 
        	data: msgData,
        	type: 'POST',
		 	context: obj,
		 	success: function(responseData){
		 		if(responseData == "true"){
		        	//Do nothing
		 		}
		 		else{
		 			alert("失败" + responseData);
		 		}
		    },
		    error: function(){
		    	alert("出错了");
		    }
	  	});
	}
}