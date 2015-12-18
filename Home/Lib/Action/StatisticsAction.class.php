<?php

	class StatisticsAction extends Action{

		public function index(){


		}

		public function _empty(){

		}

		/*
		 * 获得所有文章总数
		 */
		public function getArticleCount(){
			$articles = M('Article');
			$count = $articles->count();
		}

		public function getArticleCountByCategory(){
			if(isset($this->_get('articleCategory'))){

			}
		}

		public function getArticleCountByUser(){
			if(isset($this->_get('userId'))){
				$articles = D('Article');
				$number = $articles->where('article_authorId='.$this->_get('userId'))->count();
				if($number){
					return $number;
				}else{
					return 0;
				}
			}
		}

		public function getArticleCountByUserAndCategory(){
			if(isset($this->_get('articleCategory')) && isset($this->_get('userId'))){

			}
		}


		public function getUserCount(){

		}

		public function 


	}