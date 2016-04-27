<?php

	/**
	 * 模板变量转义
	 */
	function escape($value){
		if (!get_magic_quotes_gpc()) {
			 $value = is_array($value) ?
                    array_map('escape', $value) :
                    addslashes($value);
		}
		return $value;
	}

	/**
	 * 去除字符串中的空格等
	 */
	function trimall($str)
	{
	    $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
	    return str_replace($qian,$hou,$str);    
	}

	/*
	function genRandInt($range)
	{
		$arr=range(1,$range);
		shuffle($arr);
		return $arr[0];
	}*/