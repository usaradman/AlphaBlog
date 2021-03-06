//#########################################################################################################
//#										评论操作
//#########################################################################################################

/*添加评论*/
function addComment(authorId, articleId, articleTitle){
	//comment_editor 为评论输入框的KindEditor对象
	var com_content = comment_editor.text();
	if(com_content === null || com_content === undefined || trim(com_content) == ""){
		showTip("输入不能为空", 500);
		return;
	}
	var comData = {
		authorId:authorId,
		articleId:articleId,
		articleTitle:articleTitle,
		content:comment_editor.html(),
	};
	$.ajax({ url: getRootPath() + "/Article/addComment", 
		data: comData,
		type: 'POST',
	 	success: function(responseData){
	 		if(responseData == "true"){
	        	location.reload();
	 		}
	 		else{
	 			alertMsg("评论失败 ",responseData);
	 		}
	    },
	    error: function(responseData){
	    	showTip("竟然出错了", 500);
	    }
  	});
}


//#########################################################################################################
//#										文章操作
//#########################################################################################################


function deleteArticle(obj, delUrl){
    if (confirm("确认要删除？")) {
        $.ajax({ url: delUrl, 
		 	context: obj.parentNode.parentNode.parentNode.parentNode.parentNode,
		 	success: function(responseData){
		 		if(responseData == "true"){
		        	this.parentNode.removeChild(this);
		 		}
		 		else{
		 			alertMsg("删除失败 ",responseData);
		 		}
		    },
		    error: function(){
		    	showTip("竟然出错了", 500);
		    }
	  	});
    }
}





//#########################################################################################################
//#										分类操作
//#########################################################################################################


/*
	添加文章分类
*/
function addCategory(){
	var cName = $("#addCategoryNameInput").val();
	if(cName === null || cName === undefined || trim(cName) == ""){
		showTip("名称不能为空", 500);
		return;
	}
	var categoryData = {
		categoryName: cName,
	};

	$.ajax({ url: getRootPath() + "/UserHome/addCategory", 
	 	context: document.body, 
	 	data: categoryData, 
	 	success: function(responseData){
	 		var status = responseData.substring(0,4);
	 		if(status == "true"){
	 			var cateId = responseData.substring(5);
	 			var sucHtml = "<tr><td>"+cName+"</td><td>0</td><td>"+
					  		"<div class=\"button-group button-group-small\">"+
							"    <button type=\"button\" class=\"category_button_modify button\" onclick=\"modifyCategory(this, " + cateId + ")\"><span class=\"icon-edit text-blue\"></span> 编辑</button>"+
							"    <button type=\"button\" class=\"category_button_delete button\" onclick=\"deleteCategory(this, " + cateId +", 0)\"><span class=\"icon-trash-o text-red\"></span> 删除</button>"+
						  	"</div></td></tr>";
	        	$("#category_table").append(sucHtml);
	        	$("#addCategoryNameInput").val("");
	 		}
	 		else{
	 			alertMsg("删除失败",responseData);
	 		}
	    },
	    error: function(){
	    	showTip("竟然出错了", 500);
	    }
  	});
}

/*
	修改分类名称
*/
function modifyCategory(obj,id){
	var newCategoryName = prompt('请输入新的分类名称');
	if(newCategoryName === null || newCategoryName === undefined || trim(newCategoryName) == ""){
		return;
	}
	var categoryData = {
		categoryId: id,
		categoryName: newCategoryName,
	};

	$.ajax({ url: getRootPath() + "/UserHome/modifyCategoryName", 
	 	context: obj.parentNode.parentNode.parentNode.firstChild.nextSibling, 
	 	data: categoryData, 
	 	success: function(responseData){
	 		var status = responseData.substring(0,4);
	 		if(status == "true"){
	        	this.innerHTML = newCategoryName;
	 		}
	 		else{
	 			alertMsg("修改失败",responseData);
	 		}
	    },
	    error: function(){
	    	showTip("竟然出错了", 500);
	    }
  	});

}

/*
	删除分类
*/
function deleteCategory(obj, delId, cateSize){
	if(cateSize != 0){
		showTip("当前分类文章不为空", 500);
		return;
	}
	var delData = {
		categoryId:delId, 
	};
	$.ajax({ url: getRootPath() + "/UserHome/deleteCategory", 
	 	context: obj.parentNode.parentNode.parentNode.parentNode,
	 	data: delData, 
	 	success: function(responseData){
	 		if(responseData == "true"){
	 			this.removeChild(obj.parentNode.parentNode.parentNode);
	 		}else{
	 			alertMsg("删除失败",responseData);
	 		}
	    },
	    error: function(){
	    	showTip("竟然出错了", 500);
	    }
  	});
}

//#########################################################################################################
//#										Message 操作
//#########################################################################################################

function leaveMessage(obj, fromId, toId){
	if(fromId == -1){
		showTip("请先登录", 500);
		return;
	}
	var message=prompt("输入留言","");
	if (message!=null && message!=""){
		var msgData = {
			fromId: fromId,
			userId: toId,
			type: 2,
			content: message,
		};
		$.ajax({ url: getRootPath() + "/User/notifyUser", 
        	data: msgData,
        	type: 'POST',
		 	context: obj,
		 	success: function(responseData){
		 		if(responseData == "true"){
		        	showTip("对方已收到留言", 500);
		 		}
		 		else{
		 			alertMsg("通知失败",responseData);
		 		}
		    },
		    error: function(){
		    	showTip("竟然出错了", 500);
		    }
	  	});
	}
}

/*
	标记为已读
*/
function markMessageChecked(msgId){
	var markData = {
		msgId: msgId,
	};
	$.ajax({ url: getRootPath() + "/UserHome/markMessageChecked", 
	 	context: document.body,
	 	data: markData, 
	 	success: function(responseData){
	 		if(responseData == "true"){
	 			//Do nothing
	 		}else{
	 			alertMsg("标记失败",responseData);
	 		}
	    },
	    error: function(){
	    	showTip("竟然出错了", 500);
	    }
  	});
}

/*
	删除消息
*/
function deleteMessage(msgId){
	var delData = {
		msgId: msgId,
	};
	$.ajax({ url: getRootPath() + "/UserHome/deleteMessage", 
	 	context: document.body,
	 	data: delData, 
	 	success: function(responseData){
	 		if(responseData == "true"){
	 			//Do nothing
	 		}else{
	 			alertMsg("删除失败",responseData);
	 		}
	    },
	    error: function(){
	    	showTip("竟然出错了", 500);
	    }
  	});
}


//#########################################################################################################
//#										 粉丝操作
//#########################################################################################################

function follow(obj, fromId, toId){
	if(fromId == -1){
		showTip("请先登录", 500);
		return;
	}
	var followData = {
		fromId: fromId,
		toId: toId,
	};
	$.ajax({ url: getRootPath() + "/User/follow", 
    	data: followData,
    	type: 'POST',
	 	context: obj,
	 	success: function(responseData){
	 		if(responseData == "true"){
	        	showTip("关注成功", 500);
	        	$(obj).attr("disabled","disabled");
	 		}
	 		else{
	 			alertMsg("关注失败",responseData);
	 		}
	    },
	    error: function(){
	    	showTip("竟然出错了", 500);
	    }
  	});
}

function unFollow(obj, fromId, toId){
	if(fromId == -1){
		showTip("请先登录", 500);
		return;
	}
	var followData = {
		fromId: fromId,
		toId: toId,
	};
	$.ajax({ url: getRootPath() + "/User/unFollow", 
    	data: followData,
    	type: 'POST',
	 	context: obj,
	 	success: function(responseData){
	 		if(responseData == "true"){
	        	showTip("取消关注成功", 500);
	        	$(obj).attr("disabled","disabled");
	 		}
	 		else{
	 			alertMsg("取消关注失败",responseData);
	 		}
	    },
	    error: function(){
	    	showTip("竟然出错了", 500);
	    }
  	});
}



//#########################################################################################################
//#										用户资料修改
//#########################################################################################################

var currentTextAreaObj;		//输入框按钮按下之后Enable button ,设置当前对象为输入框

/*
	提交按钮可用
*/
function enableSubmitButton(obj){
	currentTextAreaObj = obj;
	buttonObj = obj.parentNode.parentNode.parentNode.getElementsByClassName("button bg-sub")[0];
	$(buttonObj).removeAttr("disabled");
}

/*
	修改用户签名
*/
function modifyUserSignature(){
	if(currentTextAreaObj !== null && currentTextAreaObj !== undefined){
		var sigData = {
			newSignature: currentTextAreaObj.value,
		};
		$.ajax({ 
			type: 'POST',
			url: getRootPath() + "/UserHome/modifyUserSignature", 
		 	context: currentTextAreaObj.parentNode.parentNode.parentNode.getElementsByClassName("button bg-sub")[0],
		 	data: sigData, 
		 	success: function(responseData){
		 		if(responseData == "true"){
		 			$(this).attr("disabled","disabled");
		 		}else{
		 			alertMsg("修改失败",responseData);
		 		}
		    },
		    error: function(){
		    	showTip("竟然出错了", 500);
		    }
	  	});
	}
}


/*
	修改用户邮箱
*/
function modifyUserEmail(){
	if(currentTextAreaObj !== null && currentTextAreaObj !== undefined){
		var emailData = {
			newEmail: currentTextAreaObj.value,
		};
		$.ajax({ url: getRootPath() + "/UserHome/modifyUserEmail", 
		 	context: currentTextAreaObj.parentNode.parentNode.parentNode.getElementsByClassName("button bg-sub")[0],
		 	data: emailData, 
		 	success: function(responseData){
		 		if(responseData == "true"){
		 			$(this).attr("disabled","disabled");
		 		}else{
		 			alertMsg("修改失败",responseData);
		 		}
		    },
		    error: function(){
		    	showTip("竟然出错了", 500);
		    }
	  	});
	}
}

/*
	上传新头像
*/
function modifyUserHeadIcon(btn){
	$.ajaxFileUpload({  
        url: getRootPath() + "/UserHome/modifyUserHeadIcon",
        secureuri: true,  
        fileElementId: 'uploadimg',     //文件选择框的id属性  
        success: function(responseData){   
            $(btn).attr("disabled","disabled");
        },
        error: function (){  
            showTip("竟然出错了", 500);
        }  
    }); 
}

