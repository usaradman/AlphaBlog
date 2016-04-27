<?php

	/**
     * 获取最新的10篇
     */
    function getLatest(){
        $articles = M('article');
        $result = $articles->query('select * from '.C('DB_PREFIX').'article order by article_createdate DESC limit 8');
        if($result){
            return $result;
        }
        else {
            return array();
        }
    }

    /**
     * 每个用户自己的分类,如果没有的话自动创建默认分类
     */
    function getCategoriesByUser($userId){
        $cates = M('Category');
        $result = $cates->where('user_id = '. $userId)->select();
        if($result){
            return $result;
        }else{
            $data['user_id'] = $userId;
            $data['category_name'] = 'Default';
            $data['category_size'] = 0;
            $insertResult = $cates->add($data);
            if($insertResult){
                return $cates->where('category_id='.$insertResult)->select();
            }else{
                return array();
            }
        }
    }

    /**
     * 获取所有分类
     */
    function getCategory(){
        $tags = M('category');
        $result = $tags->select();
        if($result){
            return $result;
        }
        else {
            return array();
        }
    }


    /**
     * 获取所有tag
     */
    function getAllTags(){
        $tags = M('tag');
        $result = $tags->select();
        if($result){
            return $result;
        }
        else {
            return array();
        }
    }



    /**
     * 给用户加积分
     * @param 用户积分， 加的积分， 等级， 用户ID
     */
    function addIntegral($integral, $addIntegral, $level, $id){
        $user = D('User');
        $integral += $addIntegral;
        if($integral >= $level*50){
            $integral = $integral - $level*50;
            $level++;
        }
        $data['user_integral'] = $integral;
        $data['user_level'] = $level;
        $user->where('user_id='.$id)->save($data);
    }