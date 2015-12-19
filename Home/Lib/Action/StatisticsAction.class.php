<?php

	class StatisticsAction extends Action{

		public function index(){
		}

		public function _empty(){
		}

		public function getArticleCount(){
		}

		public function getArticleCountByCategory(){
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
		}

		public function getUserCount(){
		}

	}