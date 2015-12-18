<?php


	class ArticlesAction extends Action {

		public function index(){
			$this->assign('LatestArticles', getLatest());
        	$this->assign('categories', getCategory());


			$this->type = $this->_get('type');
			$this->typevalue = $this->_get('typevalue');
			$page = isset($_GET['page']) ? $_GET['page'] : 1;


			switch($this->_get('type')){
				case 'username':{
					$this->UserName($this->_get('typevalue'),$page);
					break;
				}
				case 'userid':{
					$this->UserId($this->_get('typevalue'),$page);
					break;
				}
				case 'tag':{
					$this->Tag($this->_get('typevalue'),$page);
					break;
				}
				case 'category':{
					$this->Category($this->_get('typevalue'),$page);
					break;
				}
				case 'search':{
					$this->Search($this->_get('keywords'),$page);
					break;
				}
				default:{
					$this->error('出错了');
				}
			}

			//模板输出
			$this->display('articlelist');
		}

		public function _empty(){
			$this->index();
		}

		/**
		 * 根据用户查找
		 */
		private function UserName($username='admin', $page=1){
			$articles = D('article');
			$articlenum = $articles->where('article_author="'.$username.'"')->count();

			$result = $articles->where('article_author="'.$username.'"')->order('article_createdate DESC')->page($page,15)->select();
			if($result){
				$cate = D('category');
				$FinalResult = array();
	            foreach($result as $article){
	                $article['author'] = $this->getUser($article['article_authorId']);

	                $catesql = 'select category_name from '. C('DB_PREFIX') . 
                        'category where category_id=' . $article['article_categoryId'];
	                $art_cate = $cate->query($catesql);
	                $article['article_category'] = $art_cate[0]['category_name'];

	                $FinalResult[] = $article;
	            }
	    		$this->assign('articles', $FinalResult);
				$this->pagenum = ceil($articlenum/15);
				$this->currentpage = $page;
			}
			else{
				$this->error('UserName抱歉，没找到文章');
			}
		}

		/**
		 * 根据tag查找文章
		 */
		private function Tag($tag=-1, $page=1){
			$article = D('article');
			$article_tag = M('article_tag');
			$articleIds = $article_tag->where("tag_id = $tag")->page($page,15)->select();
			if($articleIds){
				$articlenum = count($articleIds);
				$articles = array();
				$cate = D('category');
				foreach($articleIds as $articleId){
					$currentArticle = $article->where("article_id = ".$articleId['article_id'])->select();
					if($currentArticle){
						$currentArticle[0]['author'] = $this->getUser($currentArticle[0]['article_authorId']);

		                $catesql = 'select category_name from '. C('DB_PREFIX') . 
	                        'category where category_id=' . $currentArticle[0]['article_categoryId'];
		                $art_cate = $cate->query($catesql);
		                $currentArticle[0]['article_category'] = $art_cate[0]['category_name'];

		                $articles[] = $currentArticle[0];
					}
				}

				$this->assign('articles', $articles);
				$this->pagenum = ceil($articlenum/15);
				$this->currentpage = $page;
			}
			else{
				$this->error('Tag 抱歉，没找到文章');
			}
		}

		/**
		 * 根据分类查找文章
		 */
		private function Category($category='', $page=1){
			$articles = D('article');
			$cates = D('category');
			$cateIds = $cates->where('category_name="'.$category.'"')->field('category_id')->select();
			$ids = array();
			foreach ($cateIds as $key) {
				$ids[] = $key['category_id'];
			}
			$condition['article_categoryId'] = array('in', $ids);
			$articlenum = $articles->where($condition)->count();

			$data = $articles->where($condition)->order('article_createdate DESC')->page($page,15)->select();
			if($data){
				$cate = D('category');
				$FinalResult = array();
	            foreach($data as $article){
	                $article['author'] = $this->getUser($article['article_authorId']);

	                $catesql = 'select category_name from '. C('DB_PREFIX') . 
                        'category where category_id=' . $article['article_categoryId'];
	                $art_cate = $cate->query($catesql);
	                $article['article_category'] = $art_cate[0]['category_name'];

	                $FinalResult[] = $article;
	            }
	    		$this->assign('articles', $FinalResult);
				$this->pagenum = ceil($articlenum/15);
				$this->currentpage = $page;
			}
			else{
				$this->error('Category 抱歉，没找到文章');
			}
		}

		/**
		 * 根据关键字查找文章
		 */
		private function Search($key='文章', $page=1){
			$articles = D('article');
			$articlenum = $articles->where('article_title LIKE "%'.$key.'%"')->count();
			
			$data = $articles->where('article_title LIKE "%'.$key.'%"'. ' OR article_content LIKE "%'.$key.'%"')->order('article_createdate DESC')->page($page,15)->select();
			if($data){
				$cate = D('category');
				$FinalResult = array();
	            foreach($data as $article){
	            	$article['article_title'] = str_ireplace($key,'<font color="red">'.$key.'</font>',$article['article_title']);
	                $article['author'] = $this->getUser($article['article_authorId']);

	                $catesql = 'select category_name from '. C('DB_PREFIX') . 
                        'category where category_id=' . $article['article_categoryId'];
	                $art_cate = $cate->query($catesql);
	                $article['article_category'] = $art_cate[0]['category_name'];

	                $FinalResult[] = $article;
	            }
	    		$this->assign('articles', $FinalResult);
				$this->pagenum = ceil($articlenum/15);
				$this->currentpage = $page;
			}
			else{
				$this->error('Search 抱歉，没找到文章');
			}
		}

		/**
		 * 获取用户信息
		 */
		private function getUser($userId){
	        $user = M('User');
	        $data = $user->find($userId);
	        if($data){
	            return $data;
	        }else{
	            return array();
	        }
	    }


	}