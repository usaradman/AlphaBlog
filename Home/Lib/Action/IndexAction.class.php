<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
    public function index(){
    	$data = isset($_GET['page']) ? $_GET['page'] : 1;
    	$this->show($data);
        $this->assign('LatestArticles', getLatest());
        $this->assign('categories', getCategory());
    	$this->display();
    }

    public function _empty(){
        $this->index();
    }

    /**
     * 传入页数,每页15条
     * @param page
     */
    protected function show($page=1){
    	$articles = M('article');
    	$articlenum = $articles->where('article_baned = 0')->count();

    	$this->assign('pagenum', ceil($articlenum/15));
    	$this->assign('currentpage', $page);

    	$result = $articles->where('article_baned = 0')->page($page,15)->order('article_createdate DESC')->select();
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
    	}
    	else {
    		$this->assign('articles', array());
    	}
		
    }

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