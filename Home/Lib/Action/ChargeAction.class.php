<?php

class ChargeAction extends Action {

	public function index($charge='index', $page=1){
		if(!$this->checkAuthority()){
			U('Charge/login', array(),'',true);
			return;
		}
		$this->assign('currentpage', $page);
		switch($charge){
			case 'index':{

				break;
			}
			case 'message':{

				break;
			}
			case 'user':{
				$this->assign('users', $this->getAllUsers($page));
				break;
			}
			case 'article':{

				break;
			}
		}
		$this->display($charge);
	}

	public function login(){
		$this->display('login');
	}

	public function _empty(){
		$this->index();
	}

	public function checkAuthority(){
		if(isset($_SESSION['LoginCharge']))
			 return true;
		return false;
	}


//#########################################################################################################
//#										用户操作
//#########################################################################################################


	/**
	 * 获取所有用户
	 */
	public function getAllUsers($page){
		$user = M('user');
		$users = $user->order('user_createdate DESC')->page($page,15)->select();
		if($users){
			return $users;
		}
		else{
			return array();
		}
	}

	/**
	 * 删除指定用户  TODO
	 */
	public function deleteUser($userId){
		if(!$this->checkAuthority()){
			echo 'Unauthorized';
			return;
		} 
		/*
		$user = M('user');
		if($user->where('user_id = '.$userId)->delete()){
			echo 'true';
		}
		else{
			echo 'false '. $user->getError();
		}
		*/
	}

	/**
	 * 给指定用户发送通知
	 */
	public function notifyUser($userId,$content){
		if(!$this->checkAuthority()){
			echo 'Unauthorized';
			return;
		} 
		$message = M('message');
		$msgData['fromid'] = 0;
		$msgData['toid'] = $userId;
		$msgData['type'] = 1;
		$msgData['content'] = $content;
		$msgData['checked'] = 0;

		$result = $message->add($msgData);
		if($result){
			echo 'true';
		}
		else{
			echo 'false '. $message->getError();
		}
	}

//#########################################################################################################
//#										登录 注销
//#########################################################################################################


	public function loginCharge($name='', $passwd=''){
		if($name=='usar' && $passwd=='usar'){
			session('LoginCharge', 'usar : '. date('y-m-d :i:s',time()));
			U('Charge/index', array(),'html',true);
			return;
		}
		$this->error('密码错误');
	}

	public function logoutCharge(){
		unset($_SESSION['LoginCharge']);
		echo 'true';
	}

}

?>