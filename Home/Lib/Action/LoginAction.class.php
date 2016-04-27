<?php

	class LoginAction extends Action {

		/**
		 * 默认转入登录界面
		 */
		public function index(){
			$this->type = 1;
			$this->display('login');
		}


		public function _empty(){
			$this->index();
		}

		/**
		 * 用户登录 处理表单
		 */
		public function rLogin(){
			if(IS_POST){
				$username = trim($this->_post('username'));
				$password = trim($this->_post('password'));
				
				$shlencryption = new userEncryption();
				$shlencryption->shlEncryption($password);	//传入

				$user = D('User');
				$resultFromDB = $user->where('user_name="'.$username.'" OR user_email="'.$username.'"')->select();
				if($resultFromDB){
					if(strcmp($resultFromDB[0]['user_pwd'], $shlencryption->to_string())==0){
						//登录成功
						session('LoginUser',$resultFromDB[0]);
						
						//设置安全验证cookie,防止CSRF攻击
						$op_ticket_value = $_SESSION['LoginUser']['user_name'].$_SESSION['LoginUser']['user_id'];
						setcookie('op_ticket', $op_ticket_value, time()+3600);

						//登录加积分2点
						addIntegral($resultFromDB[0]['user_integral'], 2, $resultFromDB[0]['user_level'], 
							$resultFromDB[0]['user_id']);
						
						U('UserHome/index', array(),'html',true);
					}
					else {
						//密码错误
						$this->error('密码错误',  '__APP__/Login.html');
					}
				}
				else {
					//用户不存在
					$this->error('用户不存在',  '__APP__/Login.html');
				}
			}
			else {
				$this->error('非法操作', '__APP__/Login.html');
			}
		}

		/**
		 * 用户注册
		 */
		public function regist(){
			$this->type = 2;
			$this->display('regist');
		}

		/**
		 * 用户注册 处理表单
		 */
		/*
		$sql = 'INSERT INTO usar_user
			(user_name,user_pwd,user_headicon,user_email,user_signature,user_head) VALUES(
				"'.$data['user_name'].'",
				"'.$data['user_pwd'].'",
				"'.$data['user_headicon'].'",
				"'.$data['user_email'].'",
				"'.$data['user_signature'].'",
				'.$data['user_head'].'
				)';
		*/
		public function rRegist(){
			if(IS_POST){
				$data['user_name'] = trim($this->_post('username'));
				//加密
				$shlencryption = new userEncryption();
				$shlencryption->shlEncryption(trim($this->_post('tpassword')));	//传入
				$data['user_pwd'] = $shlencryption->to_string();
				$data['user_email'] = $this->_post('email');
				$data['user_signature'] = $this->_post('signature');

				// 文件 另外处理 $headicon = $this->_post('headicon');

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
				    $upload->saveRule = 'com_create_guid'; //采用GUID序列命名

				    if(!$upload->upload()) {
				    	$this->error('图片上传失败！'.$upload->getErrorMsg());
				    }else{
				    	$info = $upload->getUploadFileInfo();
						 //保存当前数据对象
						$data['user_headicon'] = 'thumb_'.$info[0]['savename'];
						//保存blob头像
						$data['user_head'] = file_get_contents('./Upload/user/images/head/'.$data['user_headicon']);
				    }

				}else{
					$data['user_headicon'] = 'icon-head.png';
					$data['user_head'] = file_get_contents('./Public/images/icon-head.png');
				}

			    $newUser = D('User');
				$result = $newUser->add($data);
				if($result){
					//TODO 注册成功，发送验证邮件，跳转到验证页面
					$loginUser = $newUser->where('user_id ='.$result)->select();
					if($loginUser){
						session('LoginUser',$loginUser[0]);
						U('UserHome/index',array(),'html',true);
					}
					else{
						U('Index/index',array(),'html',true);
					}	
				}
				else {
					//数据库存储失败，删除图片
					if($data['user_headicon']!=''){
						unlink('./Upload/user/images/head/'.$data['user_headicon']);
					}
					$uploadFileName = './Upload/user/images/head/'.$data['user_headicon'];
					$this->error('创建失败: '. $newUser->getError());
				}

			}
			else {
				$this->error('不是POST:'.$_SERVER['REQUEST_METHOD']);
			}
			
		}

		/**
		 * 注销  退出登录
		 */
		public function logout(){
			session('LoginUser',null);
			U('index', array(),'html',true);	//跳转到登录界面
		}

		/**
		 * 发送验证邮件
		 */
		public function sendCheckEmail(){

		}

		/**
		 * 忘记密码
		 */
		public function forgetPassword(){

		}

		/**
		 * 根据邮箱检查用户是否存在，将用户密码置为随机的6位数存入，然后发送找回密码邮件
		 */
		public function sendGetPasswordEmail(){

		}


		/**
		 * 判断用户是否已经注册
		 */
		public function isUserExist(){
			$username = $this->_get('username');
			$Users = D('User');
			$data = $Users->where('user_name="'.$username.'"')->count();
			if($data){
				if($data>0){
					echo 'false';	//{"getdata":"false"}
				}
				else {
					echo '{"getdata":"true"}';
				}
			}
			else{
				echo '{"getdata":"true"}';
			}
		}


	}


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