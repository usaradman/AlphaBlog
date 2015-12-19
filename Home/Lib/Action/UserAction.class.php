<?php


	class UserAction extends Action {

		public function index($id, $page=1){
			if($id!=-1 && $this->getUserInfo($id,$page)){
				$this->display('index');
			}else{
				$this->error('未找到此用户');
			}
		}

		private function getUserInfo($id, $page=1){
			$user = D('User');
			$result = $user->where('user_id='.$id)->select();
			if($result){
				//更新访问次数
				$result[0]['user_visited']++;
				$updatedata['user_visited'] = $result[0]['user_visited'];
				$user->where('user_id='.$id)->save($updatedata);

				$this->assign('cur_user', $result[0]);
				$follow = D('Follow');
				$followNum = $follow->where('follow_id='.$id)->count();
				$fanNum = $follow->where('user_id='.$id)->count();
				$this->assign('followNum', $followNum);
				$this->assign('fanNum', $fanNum);

				//获取用户分类
				$this->assign('categories', getCategoriesByUser($id));

				//Latest articles
				$articles = D('article');
				$articlenum = $articles->where('article_authorId='.$id)->count();
				$this->assign('pagenum', ceil($articlenum/15));
    			$this->assign('currentpage', $page);
				$result = $articles->where('article_authorId='.$id)->order('article_createdate DESC')->page($page,15)->select();
	    		if($result){
	    			$cate = D('category');
		            $FinalResult = array();
		            foreach($result as $article){
		                $catesql = 'select category_name from '. C('DB_PREFIX') . 
		                        'category where category_id=' . $article['article_categoryId'];
		                $art_cate = $cate->query($catesql);
		                $article['article_category'] = $art_cate[0]['category_name'];

		                $FinalResult[] = $article;
		            }
		    		$this->assign('articles', $FinalResult);
	    			$this->assign('articlesNum', count($result));

	    		}else{
	    			$this->assign('articlesNum', 0);
	    			$this->assign('articles', array());
	    		}

				return true;
			}

			return false;
		}

		public function getUserHeaderFromUserId(){
			$user = D('User');
			$result = $user->where('user_id='.$this->_get('userId'))->select();
			if($result){
				header('Content-Type: image/jpeg');
				$im = imagecreatefromstring($result[0]['user_head']);
				if ($im !== false) {
					imagejpeg($im);
				    imagedestroy($im);
			    }else{
			    	echo 'Error';
			    }
			}else{
				$this->error("没有找到".$user->getError());
			}
		}




	}