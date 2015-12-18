<?php


	class UserModel extends Model{
		
		
		protected $_validate = array(
		    array('username','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		  );
	}