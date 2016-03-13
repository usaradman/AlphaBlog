<?php

class ChargeAction extends Action {

	public function index($charge='index', $page=1){
		if(!$this->checkAuthority()){
			U('Charge/login', array(),'html',true);
			return;
		}
		$this->assign('currentpage', $page);
		switch($charge){
			case 'index':{

				break;
			}
			case 'user':{
				$this->assign('users', $this->getAllUsers($page));
				break;
			}
			case 'article':{
				$this->assign('articles', 
					$this->getAllArticles($this->_param('userId', 'htmlspecialchars,strip_tags', -1), $page));
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
//#										消息通知
//#########################################################################################################

	/**
	 * 给所有用户发送通知
	 */
	public function notifyAllUser($fromId, $type, $content){
		if(!$this->checkAuthority()){
			echo 'Unauthorized';
			return;
		}
		$result = R('Message/notifyAllUser',array($fromId, $type, $content));
		if($result){
			echo 'true';
		}
		else{
			echo 'false';
		}
	}

	/**
	 * 给指定用户发送通知
	 */
	public function notifyUser($fromId, $userId, $type, $content){
		if(!$this->checkAuthority()){
			echo 'Unauthorized';
			return;
		}
		$result = R('Message/notifyUser',array($fromId, $userId, $type, $content));
		if($result){
			echo 'true';
		}
		else{
			echo 'false';
		}
	}


//#########################################################################################################
//#										文章操作
//#########################################################################################################
	/**
	 * 获取所有文章
	 */
	private function getAllArticles($userId=-1 , $page=1){
		$user = M('user');
		$article = M('article');

		$articles = false;
		if($userId >= 0){
			$articles = $article->where('article_authorId ='.$userId)->order('article_createdate DESC')->page($page,15)->select();
			$this->pagenum = ceil(($article->where('article_authorId ='.$userId)->count())/15);
		}
		else{
			$articles = $article->order('article_createdate DESC')->page($page,15)->select();
			$this->pagenum = ceil(($article->count())/15);
		}
		
		//设定页面数量
		if($articles){
			$articleResult = array();
            foreach($articles as $art){
            	//获取作者信息
            	$userInfo = $user->field('user_id,user_name')->find($art['article_authorId']);
		        if($userInfo){
		        	$art['author'] = $userInfo;
		        }else{
		            $art['author'] = array();
		        }
                $articleResult[] = $art;
            }
			return $articleResult;
		}
		else{
			return array();
		}
	}

	/**
	 * 删除文章 在ArticleAction.delete
	 */


	/**
	 * 审核禁止文章
	 */
	public function banArticle($articleId, $isBan){
		if(!$this->checkAuthority()){
			echo 'Unauthorized';
			return;
		}
		$article = M('article');
		$banData['article_baned'] = $isBan;
		if($article->where("article_id = $articleId")->save($banData)){
			echo 'true';
		}
		else{
			echo 'false '.$article->getError();
		}
	}





//#########################################################################################################
//#										用户操作
//#########################################################################################################


	/**
	 * 获取所有用户
	 */
	private function getAllUsers($page){
		$user = M('user');
		$this->pagenum = ceil($user->count()/15);
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


//#########################################################################################################
//#										登录 注销
//#########################################################################################################


	public function loginCharge($username='', $password=''){
		if($username=='usar' && $password=='usar'){
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