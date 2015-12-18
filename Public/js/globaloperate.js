

function deleteArticle(obj, delUrl){
    if (confirm("确认要删除？")) {
        $.ajax({ url: delUrl, 
		 	context: obj.parentNode.parentNode.parentNode.parentNode.parentNode,
		 	success: function(responseData){
		 		if(responseData == "true"){
		        	this.parentNode.removeChild(this);
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





//#########################################################################################################
//#										文章分类
//#########################################################################################################


/*
	添加文章分类
*/
function addCategory(){
	var cName = $("#addCategoryNameInput").val();
	if(cName === null || cName === undefined || trim(cName) == ""){
		alert("名称不能为空");
		return;
	}
	var categoryData = {
		categoryName: cName,
	};

	$.ajax({ url: "UserHome/addCategory", 
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
	 			alert("添加失败");
	 		}
	    },
	    error: function(){
	    	alert("出错了");
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

	$.ajax({ url: "UserHome/modifyCategoryName", 
	 	context: obj.parentNode.parentNode.parentNode.firstChild.nextSibling, 
	 	data: categoryData, 
	 	success: function(responseData){
	 		var status = responseData.substring(0,4);
	 		if(status == "true"){
	        	this.innerHTML = newCategoryName;
	 		}
	 		else{
	 			alert("修改失败");
	 		}
	    },
	    error: function(){
	    	alert("出错了");
	    }
  	});

}

/*
	删除分类
*/
function deleteCategory(obj, delId, cateSize){
	if(cateSize != 0){
		alert("当前分类文章不为空");
		return;
	}
	var delData = {
		categoryId:delId, 
	};
	$.ajax({ url: "UserHome/deleteCategory", 
	 	context: obj.parentNode.parentNode.parentNode.parentNode,
	 	data: delData, 
	 	success: function(responseData){
	 		if(responseData == "true"){
	 			this.removeChild(obj.parentNode.parentNode.parentNode);
	 		}else{
	 			alert("删除失败");
	 		}
	    },
	    error: function(){
	    	alert("出错了");
	    }
  	});
}

//#########################################################################################################
//#										Message 操作
//#########################################################################################################

/*
	标记为已读
*/
function markMessageChecked(msgId){
	var markData = {
		msgId: msgId,
	};
	$.ajax({ url: "UserHome/markMessageChecked", 
	 	context: document.body,
	 	data: markData, 
	 	success: function(responseData){
	 		if(responseData == "true"){
	 			//Do nothing
	 		}else{
	 			console.log("False 标记为已读失败");
	 		}
	    },
	    error: function(){
	    	alert("出错了");
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
	$.ajax({ url: "UserHome/deleteMessage", 
	 	context: document.body,
	 	data: delData, 
	 	success: function(responseData){
	 		if(responseData == "true"){
	 			//Do nothing
	 		}else{
	 			console.log("False 删除失败");
	 		}
	    },
	    error: function(){
	    	alert("出错了");
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
			url: "UserHome/modifyUserSignature", 
		 	context: currentTextAreaObj.parentNode.parentNode.parentNode.getElementsByClassName("button bg-sub")[0],
		 	data: sigData, 
		 	success: function(responseData){
		 		if(responseData == "true"){
		 			$(this).attr("disabled","disabled");
		 		}else{
		 			alert("修改失败 "+responseData);
		 		}
		    },
		    error: function(){
		    	alert("出错了");
		    }
	  	});
	}
}



function modifyUserEmail(){
	if(currentTextAreaObj !== null && currentTextAreaObj !== undefined){
		var emailData = {
			newEmail: currentTextAreaObj.value,
		};
		$.ajax({ url: "UserHome/modifyUserEmail", 
		 	context: currentTextAreaObj.parentNode.parentNode.parentNode.getElementsByClassName("button bg-sub")[0],
		 	data: emailData, 
		 	success: function(responseData){
		 		if(responseData == "true"){
		 			$(this).attr("disabled","disabled");
		 		}else{
		 			alert("修改失败 "+responseData);
		 		}
		    },
		    error: function(){
		    	alert("出错了");
		    }
	  	});
	}
}

/*
	上传新头像
*/
function modifyUserHeadIcon(btn){
	$.ajaxFileUpload({  
        url: "UserHome/modifyUserHeadIcon",
        secureuri: true,  
        fileElementId: 'uploadimg',     //文件选择框的id属性  
        success: function(responseData){   
            $(btn).attr("disabled","disabled");
        },
        error: function (data, status, e){  
            alert("出错了");
        }  
    }); 
}



//#########################################################################################################
//#										杂项
//#########################################################################################################


function trim(str){ //删除左右两端的空格
	if(str=="") return str;
　　return str.replace(/(^\s*)|(\s*$)/g, "");
}