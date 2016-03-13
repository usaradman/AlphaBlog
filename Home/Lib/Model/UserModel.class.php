<?php


	class UserModel extends Model{
		
		protected $_validate = array(
		    array('username','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		  );








		/**
		 * 获取所有粉丝
		 */
		public function getFans($userId){
			$follow = M('follow');
			$user = M('user');
			$fansId = $follow->where('follow_id = '. $userId)->select();
			foreach ($fansId as $item) {
				$fan = $user->where('user_id = '.$item['user_id'])->select();
				$fans[] = $fan[0];
			}
			return $fans;
		}

		public function getFollowers($userId){
			$follow = M('follow');
			$user = M('user');
			$followerId = $follow->where('user_id = '. $userId)->select();
			foreach ($followerId as $item) {
				$fol = $user->where('user_id = '.$item['follow_id'])->select();
				$fols[] = $fol[0];
			}
			return $fols;
		}

	}