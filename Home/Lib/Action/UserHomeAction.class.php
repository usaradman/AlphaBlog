<?php
	

	/*
	个人登录后自己的主页

	*/
	class UserHomeAction extends Action {

		/**
		 * 用户主页
		 */
		public function index(){
			if($this->checkAuthority()){
				$this->getPublicInfo();

		    	$this->show();
		    	$this->display('home');
			}else{
				$this->error('请先登录', U('/login'));
			}
			
		}

		/**
		 * 文章界面
		 */
		public function articles(){
	    	if($this->checkAuthority()){
				$this->getPublicInfo();

				$page = trimall($this->_get("page","strip_tags", 1));
				$cate = trimall($this->_get("category","strip_tags", -1));
		    	$this->assign('articles', $this->getArticles($page, $cate));
		    	$this->display('articles');
			}else{
				$this->error('请先登录', U('/login'));
			}
	    }

	    public function manage(){
	    	if($this->checkAuthority()){
				$this->getPublicInfo();

				$page = trimall($this->_get('charge','strip_tags', 'category'));
				switch($page){
					case 'category':{
						$this->display('manage_category');
						break;
					}
					case 'message':{
						$this->display('manage_message');
						break;
					}
					case 'follow':{
						$this->display('manage_follow');
						break;
					}
					case 'fan':{
						$this->display('manage_fan');
						break;
					}
					case 'web':{
						$this->display('manage_web');
						break;
					}
					case 'profile':{
						$this->display('manage_profile');
						break;
					}
					default:{
						$this->error('出错了');
					}
				}
		    	
			}else{
				$this->error('请先登录', U('/login'));
			}
	    }


		private function getPublicInfo(){
			$user = session('LoginUser');
			$this->assign('pagetitle', ' | 个人中心');
			$this->assign('keywords', 'Insist Blog, '. $user['user_name']);
			$this->assign('description', $user['user_name'].', '.$user['user_signature']);
			//文章分类
	    	$this->assign('categories', getCategoriesByUser($user['user_id']));
	    	$this->assign('messages', R('Message/getMessagesByUser',array($user['user_id'])));

	    	$modelUser = D('User');
	    	$fans = $modelUser->getFans($user['user_id']);
	    	$fols = $modelUser->getFollowers($user['user_id']);
	    	$this->assign('fans',$fans);
	    	$this->assign('fansNumber',count($fans));
	    	$this->assign('followers', $fols);
	    	$this->assign('followersNumber', count($fols));

		}


		/**
	     * 传入页数,每页15条
	     * @param page
	     */
	    protected function show(){
	    	$user = session('LoginUser');
	    	$articles = M('article');

	    	//用户文章数量
	    	$articlenum = $articles->where('article_authorId='.$user['user_id'])->count();
	    	$this->assign('articleNumber',$articlenum);	

	    	//关注和粉丝数
	    	$follow = D('Follow');
			$followNum = $follow->where('follow_id='.$id)->count();
			$fanNum = $follow->where('user_id='.$id)->count();
			$this->assign('followNum', $followNum);
			$this->assign('fanNum', $fanNum);

	    }

	    /**
	     * 根据分类查找，按时间逆序排列
	     * 考虑分页
	     */
	    private function getArticles($page=1, $categoryId=-1){
	    	$user = session('LoginUser');
	    	$articles = M('article');

	    	//用户文章数量
	    	$articlenum = 0;
	    	if($categoryId!=-1){
	    		$articlenum = $articles->where('article_categoryId='.$categoryId)->count();
	    	}else{
	    		$articlenum = $articles->where('article_authorId='.$user['user_id'])->count();
	    	}
	    	$this->assign('articleNumber',$articlenum);	

	    	//总共页数和当前页数
	    	$this->assign('pagenum', ceil($articlenum/15));
	    	$this->assign('currentpage', $page);
	    	$cate = D('category');
	    	$category = $cate->where('category_id='.$categoryId)->select();
	    	if($category){
	    		$this->assign('current_category', $category[0]['category_name']);
	    	}else{
	    		$this->assign('current_category', '所有');
	    	}

	    	$result = null;
	    	if($categoryId!=-1){
	    		$result = $articles->field('article_content',true)->where('article_categoryId='.$categoryId)
	    						->order('article_createdate DESC')->page($page,15)->select();
	    	}else{
	    		$result = $articles->field('article_content',true)->where('article_authorId='.$user['user_id'])
	    						->order('article_createdate DESC')->page($page,15)->select();
	    	}
	    	if($result){
	    		return $result;
	    	}

	    	return array();
	    }
	    


//#########################################################################################################
//#										Category 操作
//#########################################################################################################

	    /**
	     * 添加文章分类
	     */
	    public function addCategory($categoryName){
	    	if($this->checkAuthority()){
		    	$user = session('LoginUser');
		    	$category = M("category");
		    	$categoryInsertData['user_id'] = $user['user_id'];
		    	$categoryInsertData['category_name'] = $categoryName;
		    	$categoryInsertData['category_size'] = 0;

		    	$addResult = $category->add($categoryInsertData);
		    	if($addResult){
		    		echo "true#$addResult";
		    	}
		    	else{
		    		echo 'false '.$category->getError();
		    	}
		    	return;
		    }else{
				$this->error('请先登录', U('/login'));
			}
	    }

	    /**
	     * 修改文章分类名称
	     */
	    public function modifyCategoryName($categoryId, $categoryName){
	    	if($this->checkAuthority()){
		    	$user = session('LoginUser');
		    	$category = M("category");
		    	$categoryUpdateData['category_name'] = $categoryName;

		    	$updateResult = $category->where("category_id = $categoryId")->save($categoryUpdateData);
		    	if($updateResult){
		    		echo 'true';
		    	}
		    	else{
		    		echo 'false '.$category->getError();
		    	}
		    	return;
		    }else{
				$this->error('请先登录', U('/login'));
			}
	    }

	    /**
	     * 删除文章分类
	     */
	    public function deleteCategory($categoryId){
	    	if($this->checkAuthority()){
		    	$category = M("category");
		    	$delResult = $category->where("category_id = $categoryId")->delete();
		    	if($delResult){
		    		echo 'true';
		    	}
		    	else{
		    		echo 'false '.$category->getError();
		    	}
		    	return;
		    }else{
				$this->error('请先登录', U('/login'));
			}
	    }


//#########################################################################################################
//#										Message 标记为已读
//#########################################################################################################

	    /**
	     * 删除未读通知
	     */
	    public function markMessageChecked($msgId){
	    	if($this->checkAuthority()){
		    	$msg = M("message");
		    	$checkData['checked'] = 1;
		    	$markResult = $msg->where("id = $msgId")->save($checkData);
		    	if($markResult){
		    		echo 'true';
		    	}
		    	else{
		    		echo 'false '.$msg->getError();
		    	}
		    	return;
		    }else{
				$this->error('请先登录', U('/login'));
			}
	    }

	    /**
	     * 删除消息
	     */
	    public function deleteMessage($msgId){
	    	if($this->checkAuthority()){
		    	$msg = M("message");
		    	$delResult = $msg->where("id = $msgId")->delete();
		    	if($delResult){
		    		echo 'true';
		    	}
		    	else{
		    		echo 'false '.$msg->getError();
		    	}
		    	return;
		    }else{
				$this->error('请先登录', U('/login'));
			}
	    }

//#########################################################################################################
//#										个人信息修改
//#########################################################################################################

	    /**
	     * 修改个人签名
	     */
	    public function modifyUserSignature($newSignature){
	    	if($this->checkAuthority()){
	    		$userOp = M('user');
	    		$user = session('LoginUser');
	    		$userData['user_signature'] = substr(htmlspecialchars($newSignature),0,100);
	    		$modifyResult = $userOp->where("user_id = ". $user['user_id'])->save($userData);
	    		if($modifyResult){
	    			$user['user_signature'] = $userData['user_signature'];
	    			session('LoginUser', $user);
	    			echo 'true';
	    		}
	    		else{
	    			echo 'false '.$userOp->getError();
	    		}
	    	}
	    }

	    /**
	     * 修改个人邮箱
	     */
	    public function modifyUserEmail($newEmail){
	    	if($this->checkAuthority()){
	    		$userOp = M('user');
	    		$user = session('LoginUser');
	    		$userData['user_email'] = $newEmail;
	    		$modifyResult = $userOp->where("user_id = ". $user['user_id'])->save($userData);
	    		if($modifyResult){
	    			$user['user_email'] = $newEmail;
	    			session('LoginUser', $user);
	    			echo 'true';
	    		}
	    		else{
	    			echo 'false '.$userOp->getError();
	    		}
	    	}
	    }

	    /**
	     * 修改个人密码
	     */
	    public function modifyUserPasswd($oldPasswd, $newPasswd){
	    	if($this->checkAuthority()){
	    		$userOp = M('user');
	    		$user = session('LoginUser');

	    		$shlencryption = new userEncryption();
				$shlencryption->shlEncryption(trim($oldPasswd));
				if(strcmp($user['user_pwd'], $shlencryption->to_string())==0){
					$shlencryption->shlEncryption(trim($newPasswd));
					$userData['user_pwd'] = $shlencryption->to_string();
					$modifyResult = $userOp->where("user_id = ". $user['user_id'])->save($userData);
		    		if($modifyResult){
		    			$user['user_pwd'] = $shlencryption->to_string();
		    			session('LoginUser', $user);
		    			echo 'true';
		    		}
		    		else{
		    			echo 'false '.$userOp->getError();
		    		}
		    		return;
				}
				else{
					echo 'false 密码错误';
				}
	    	}
	    }


	    /**
	     * 修改个人头像
	     */
	    public function modifyUserHeadIcon(){
	    	if($this->checkAuthority()){
	    		$userOp = M('user');
	    		$user = session('LoginUser');
	    		$userData = array();

	    		//文件操作
	    		if(!empty($_FILES['headicon']['name'])){
					import('ORG.Net.UploadFile');
				    $upload = new UploadFile();// 实例化上传类
				     //设置需要生成缩略图，仅对图像文件有效
					$upload->thumb = true;
					 //设置需要生成缩略图的文件后缀
					$upload->thumbPrefix = 'thumb_';  //生产1张缩略图
					 //设置缩略图最大宽度
					$upload->thumbMaxWidth = '200';
					 //设置缩略图最大高度
					$upload->thumbMaxHeight = '200';	
					$upload->thumbRemoveOrigin = true;	//移出原图
				    $upload->maxSize  = 3145728;// 设置附件上传大小
				    $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
				    $upload->savePath = './Upload/user/images/head/';// 设置附件上传目录
				    $upload->saveRule = 'com_create_guid'; // 采用GUID序列命名

				    if(!$upload->upload()) {
				    	$this->error('图片上传失败！'.$upload->getErrorMsg());
				    }else{
				    	$info = $upload->getUploadFileInfo();
						 //保存当前数据对象
						$userData['user_headicon'] = 'thumb_'.$info[0]['savename'];
						//保存blob头像
						$userData['user_head'] = file_get_contents('./Upload/user/images/head/'.$userData['user_headicon']);
				    }

				}else{
					$userData['user_headicon'] = '';
					$userData['user_head'] = file_get_contents('./Upload/user/images/head/logo-64.ico');
				}

	    		$modifyResult = $userOp->where("user_id = ". $user['user_id'])->save($userData);
	    		if($modifyResult){
	    			//删除以前的图片
	    			unlink('./Upload/user/images/head/'.$user['user_headicon']);
	    			$user['user_headicon'] = $userData['user_headicon'];
	    			session('LoginUser', $user);
	    			echo 'true';
	    		}
	    		else{
	    			echo 'false '.$userOp->getError();
	    		}
	    	}
	    }


//#########################################################################################################
//#										杂项
//#########################################################################################################


	    private function getUser($userId){
	        $user = M('User');
	        $data = $user->find($userId);
	        if($data){
	            return $data;
	        }else{
	            return array();
	        }
	    }

	    /**
		 * 判断用户是否登录
		 */
		private function checkAuthority(){
			if(isset($_SESSION['LoginUser'])) return true;
			return false;
		}



	}



//#########################################################################################################
//#										加密类
//#########################################################################################################



	class userEncryption
	{
		var $enstr = null;
		function shlEncryption($str)
		{
			$this->enstr = $str;
		}
		function get_shal()
		{
			return sha1($this->enstr);
		}
		function get_md5()
		{
			return md5($this->enstr);
		}
		function get_jxqy3()
		{
			$tmpMS = $this->get_shal().$this->get_md5();
			$tmpNewStr = substr($tmpMS,0,9).'s'
					.substr($tmpMS,10,9).'h'.substr($tmpMS,20,9).'l'
					.substr($tmpMS,30,9).'s'.substr($tmpMS,40,9).'u'
					.substr($tmpMS,50,9).'n'.substr($tmpMS,60,9).'y'
					.substr($tmpMS,70,2);
			$tmpNewStr = substr($tmpNewStr,-36).substr($tmpNewStr,0,36);
			$tmpNewStr = substr($tmpNewStr,0,70);
			$tmpNewStr = substr($tmpNewStr,0,14).'j'.substr($tmpNewStr,14,14).'x'.substr($tmpNewStr,28,14).'q'.substr($tmpNewStr,32,14).'y'.substr($tmpNewStr,56,14).'3';
			return $tmpNewStr;
		}
		function to_string()
		{
			$tmpstr = $this->get_jxqy3();
			$tmpstr = substr($tmpstr,-35).substr($tmpstr,0,40);
			return $tmpstr;
		}
	}