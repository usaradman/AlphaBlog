<?php

/**
 * 供其他Action使用R方法调用
 */

class MessageAction extends Action {

	public function _empty(){
		echo 'method not exist';
	}

	/**
     * 获取每个用户的消息列表
     */
    public function getMessagesByUser($userId){
        $msg = M('message');
        $result = $msg->where("toid = $userId")->order('createtime DESC')->select();
        if($result){
        	$msgs = array();
        	foreach ($result as $msg) {
        		$msg['content'] = stripcslashes($msg['content']);
        		$msgs[] = $msg;
        	}
            return $msgs;
        }else{
            return array();
        }
    }

	/**
	 * 给所有人发送消息
	 * @return bool
	 */
	public function notifyAllUser($fromId, $type, $content){
		$message = M('message');
		$user = M('user');
		$msgData['fromid'] = $fromId;
		$msgData['content'] = $content;
		$msgData['checked'] = 0;

		$allUser = $user->field('user_id')->select();
		if($allUser){
			foreach($allUser as $cUser){
				$msgData['toid'] = $cUser['user_id'];
				if(!$message->add($msgData)){
					return false;
				}
			}
			return true;
		}
		else{
			return false;
		}
	}

	/**
	 * 给指定用户发送消息,可以是多个用户
	 * @return bool
	 */
	public function notifyUser($fromId, $userId, $type, $content){
		$message = M('message');
		$msgData['fromid'] = $fromId;
		$msgData['type'] = $type;
		$msgData['content'] = $content;
		$msgData['checked'] = 0;

		if(is_array($userId)){
			foreach($userId as $id){
				$msgData['toid'] = $id;
				if(!$message->add($msgData)){
					return false;
				}
			}
			return true;
		}
		else{
			$msgData['toid'] = $userId;
			if($message->add($msgData)){
				return true;
			}
			else{
				return false;
			}
		}
	}





}


?>