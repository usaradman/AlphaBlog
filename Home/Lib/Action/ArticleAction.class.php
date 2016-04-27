<?php

/**
 *	文章控制器
 */
class ArticleAction extends Action {

	public function index(){
		if(isset($_GET['id'])){
			$this->show(trim($this->_get('id')));
		}
		else {
			$this->error('出错了');
		}
	}

	public function _empty(){
		$this->index();
	}

	/**
	 * 展示文章 根据articleid
	 */
	public function show($id){
		$this->assign('LatestArticles', getLatest());
		$this->assign('allTags', getAllTags());

		$articles = M('article');
		$data = $articles->find($id);
		if($data){
			//上一篇
			$pre = $articles->where('article_id<'.$id.' AND article_authorId='.$data['article_authorId'])
					->field('article_id, article_title')->find();
			if($pre)
				$this->assign('preArticle', $pre);
			
			//下一篇
			$next = $articles->where('article_id>'.$id.' AND article_authorId='.$data['article_authorId'])
					->field('article_id, article_title')->find();
			if($next)
				$this->assign('nextArticle', $next);


			$data['article_content'] = stripcslashes($data['article_content']);
			$this->assign('article', $data);
			$this->getComment($this->_get('id'));
			
			//获取文章tags
			$tag = M('tag');
			$arttag = M('article_tag');
			$art_tags = $arttag->where('article_id='.$this->_get('id'))->select();
			if($art_tags){
				$tags = array();
				foreach($art_tags as $art_tag){
					$realTag = $tag->where('tag_id='.$art_tag['tag_id'])->select();
					$tags[] = $realTag[0];
				}
				$this->assign('tags', $tags);
			}else{
				$this->assign('tags', array());
			}

			$article = M('article');
			$sql = 'update '. C('DB_PREFIX') .'article set article_readnum=article_readnum+1 where article_id='.$id;
			$article->execute($sql);

			$this->assign('pagetitle', ' | '.$data['article_title']);
			$this->assign('keywords', 'Insist Blog, '. $data['article_title']);
			$this->assign('description', $data['article_title']);
		}
		else{
			$this->error('抱歉，没找到文章');
		}
		$this->display('article');		//模板输出
	}


	/**
	 * 获取文章评论
	 */
	private function getComment($curId){
		$comment = M('comment');
		$result = $comment->where('comment_articleid='.$curId)->select();
		if($result){
			$coms = array();
			foreach ($result as $com) {
				$com['content'] = stripcslashes($com['content']);
				$coms[] = $com;
			}
			$this->assign('comments', $coms);
		}
	}

	/**
	 * 添加评论
	 */
	public function addComment($authorId, $articleId, $articleTitle, $content){
		if($this->checkAuthority()){
			$comment = M('Comment');
			$cUser = session('LoginUser');
			$data['comment_articleid'] = $articleId;
			$data['comment_userid'] = $cUser['user_id'];
			$data['comment_username'] =  $cUser['user_name'];
			$data['comment_content'] = $content;
			$data['article_checked'] = 0;
			
			if($comment->add($data)) {
				$article = M('article');
				$sql = 'update '.C('DB_PREFIX').'article set article_cmtnum=article_cmtnum+1 where article_id='.$articleId;
				$article->execute($sql);

				//发送评论通知
				$content = '<a class="text-blue" href="__APP__/user/'. $cUser['user_id'] . '.html" target="_blank">' . $cUser['user_name'] . '</a> 评论您的文章'.
								'<a class="text-blue" href="__APP__/article/' . $articleId . '.html" target="_blank"> ' . $articleTitle . '</a>:  ' . $content;
				
				$fromId = $cUser['user_id'];
				$userId = $authorId;
				$type = 3;
				$result = R('Message/notifyUser',array($fromId, $userId, $type, $content));
				echo 'true';
				return;
		    }
		    else{
		    	echo '出错了';
		        echo 'false '.$comment->getError();
		        return;
		    }
	    }
		else{
			echo '请先登录';
			return;
		}
	}


	/**
	 * 修改文章
	 */
	public function edit(){
		$this->assign('LatestArticles', getLatest());

		if($this->checkAuthority()){
			$cUser = session('LoginUser');
        	$this->assign('categories', getCategoriesByUser($cUser['user_id']));

			$article = D('article');
			$data = $article->find($this->_get('id'));
			if($data){
				$data['article_content'] = stripcslashes($data['article_content']);

				//获取文章类别
				$cate = D('category');
				$catesql = 'select category_name from '. C('DB_PREFIX') . 
						'category where category_id=' . $data['article_categoryId'];
				$art_cate = $cate->query($catesql);
				$data['article_category'] = $art_cate[0]['category_name'];
				$this->assign('article', $data);

				//获取文章tags
				$tag = M('tag');
				$arttag = M('article_tag');
				$art_tags = $arttag->where('article_id='.$this->_get('id'))->select();
				if($art_tags){
					$tags = '';
					foreach($art_tags as $art_tag){
						$realTag = $tag->where('tag_id='.$art_tag['tag_id'])->select();
						$tags = $tags. $realTag[0]['tag_name'] . '，';
					}
					$this->assign('tags', $tags);
				}else{
					$this->assign('tags', '');
				}
				
				$this->assign('security_ticket', md5($_COOKIE['op_ticket']));	//安全码
				$this->display('edit');
			}
			else{
				$this->error('抱歉，没找到资源');
			}
		}
		else{
			$this->error('请先登录', U('/login'));
		}
	}

	/**
	 * 修改后的文章进行更新
	 */
	public function update(){
		if($this->checkAuthority() && $_POST['security_ticket'] == md5($_COOKIE['op_ticket'])){
			$currentUser = session('LoginUser');
			$Form = D('Article');
			
			if(trim($_REQUEST['title'])==''){
				$this->error('标题不能为空');
			}
			if(trim($_REQUEST['content'])==''){
				$this->error('内容不能为空');
			}

			//先进行修改tag
			//添加文章tags
			$tag = M('tag');
			$tagStr = str_replace('，', ',', $this->_post('tags',"strip_tags"));
			$tags = explode(',', $tagStr);
			foreach ($tags as $tagName) {
				if(trim($tagName)!=''){
					$data['tag_name'] = trim($tagName);
					$tag->add($data);
				}
			}
			//清空旧的关联关系
			$articleTag = D('article_tag');
			$articleTag->where('article_id='.$this->_get('id'))->delete();
			//更新article和tag对应关系表
			foreach ($tags as $tagName) {
				$tagId = $tag->field('tag_id')->where('tag_name="'.trim($tagName).'"')->select();
				if($tagId){
					$arttagdata['article_id'] = $this->_get('id');
					$arttagdata['tag_id'] = $tagId[0]['tag_id'];
					$articleTag->add($arttagdata);
				}
			}

			$data = array();
			//首先获取原来的文章信息
			$oldAritcle = $Form->where('article_id='.$this->_get('id'))->select();
			if($oldAritcle){
				if($oldAritcle[0]['article_title'] != $this->_post('title',"strip_tags")){
					$data['article_title'] = $this->_post('title',"strip_tags");
				}
				if($oldAritcle[0]['article_authorId'] != $currentUser['user_id']){
					$data['article_authorId'] = $currentUser['user_id'];
				}
				if($oldAritcle[0]['article_author'] != $currentUser['user_name']){
					$data['article_author'] = $currentUser['user_name'];
				}
				if($oldAritcle[0]['article_content'] != $_POST['content']){
					$data['article_content'] = $_POST['content'];
				}
				if($oldAritcle[0]['article_categoryId'] != $_POST['category']){
					$data['article_categoryId'] = $_POST['category'];
				}
			}
			else{
				$this->error('更新出现错误 找不到对应文章');
			}

			//检查需不需要更新
			if(count($data) == 0){
				$this->success('修改成功', U('UserHome/articles'));
				return;
			}
			//开始更新文章本身
	        $result = $Form->where('article_id='.$this->_get('id'))->save($data);
	        if($result) {
	        	//TODO 更新后会有未删除的图片
	        	$articleImg = M('ArticleImg');
				$imgPaths = explode("-",$this->_post('paths'));
				$pathsql = 'insert into '.C('DB_PREFIX').'articleImg values ('.$this->_get('id').', "';
				foreach($imgPaths as $path){
					if(trim($path)!=""){
						$dpathsql = $pathsql . $path.'")';
						$articleImg->query($dpathsql);
					}
				}

				//修改分类数量标记
				$cate = D('category');
				$catesql = 'update '. C('DB_PREFIX') . 
							'category set category_size = category_size +1 where category_id='.$_REQUEST['category'];
				$cate->execute($catesql);			
				$catesql = 'update '. C('DB_PREFIX') . 
							'category set category_size = category_size -1 where category_id='.$_REQUEST['old_category'];
				$cate->execute($catesql);

	            $this->success('修改成功', U('UserHome/articles'));

	        }else{
	            $this->error('更新出现错误:'.$Form->getError());
	        }

		}
		else{
			$this->error('请先登录', U('/login'));
		}
	}

	/**
	 * 添加文章
	 */
	public function add(){
		if($this->checkAuthority()){
			$cUser = session('LoginUser');
        	$this->assign('categories', getCategoriesByUser($cUser['user_id']));

			$this->assign('security_ticket', md5($_COOKIE['op_ticket']));	//安全码
			$this->display('add');
		}
		else{
			$this->error('请先登录', U('/login'));
		}
	}

	/**
	 * 把新添加的文章进行保存
	 */
	public function save(){
		// 保存操作，然后跳转到相应文章界面
		if($this->checkAuthority() && $_POST['security_ticket'] == md5($_COOKIE['op_ticket'])){
			$currentUser = session('LoginUser');
			$article = D('Article');
			$data['article_title'] = $this->_post('title',"strip_tags");
			if(trim($_REQUEST['title'])==''){
				$this->error('标题不能为空');
			}
			$data['article_authorId'] = $currentUser['user_id'];
			$data['article_author'] = $currentUser['user_name'];
			$data['article_content'] = $_POST['content'];
			if(trim($_REQUEST['content'])==''){
				$this->error('内容不能为空');
			}
			$data['article_categoryId'] = $_POST['category'];

			$result = $article->add($data);
			if($result){
				//存储图片路径
				$articleImg = M('ArticleImg');
				$imgPaths = explode("-",$this->_post('paths'));
				$pathsql = 'insert into '.C('DB_PREFIX').'articleImg values ('.$result.', "';
				foreach($imgPaths as $path){
					if(trim($path)!=''){
						$dpathsql = $pathsql . $path.'")';
						$articleImg->query($dpathsql);
					}
				}

				//添加文章tags
				$tag = M('tag');
				$tagStr = str_replace('，', ',', $this->_post('tags',"strip_tags"));
				$tags = explode(',', $tagStr);
				foreach ($tags as $tagName) {
					if(trim($tagName)!=''){
						$tagdata['tag_name'] = trim($tagName);
						$tag->add($tagdata);
					}
				}
				//更新article和tag对应关系表
				$articleTag = D('article_tag');
				foreach ($tags as $tagName) {
					$tagId = $tag->field('tag_id')->where('tag_name="'.trim($tagName).'"')->select();
					$arttagdata['article_id'] = $result;
					$arttagdata['tag_id'] = $tagId[0]['tag_id'];
					$articleTag->add($arttagdata);
				}


				//更改category_size
				$category = M('category');
				$sql = 'update '.C('DB_PREFIX').'category set category_size=category_size+1 where category_id="'.$_POST['category'].'"';
				$category->query($sql);
				//给用户添加积分5
				addIntegral($currentUser['user_integral'], 5, $currentUser['user_level'], $currentUser['user_id']);

				$this->success('添加成功',U('UserHome/index'));
				
			}
			else{
				$this->error($article->getError().'添加文章出错');
			}
		}
		else{
			$this->error('请先登录', U('/login'));
		}
	}


	/**
	 *  删除文章
	 */
	public function delete(){
		if($this->checkAuthority() || isset($_SESSION['LoginCharge'])){
			$article = D('Article');
			$delId = $this->_get('id',"strip_tags");
			$curArticle = $article->where('article_id='.$delId)->select();
			$errorMsg = "";

			//删除文章
			$delArticleResult = $article->where('article_id='.$delId)->delete();
			if($delArticleResult){
				//更改category_size
				$category = M('category');
				$sql = 'update '.C('DB_PREFIX').'category set category_size=category_size-1 where category_id="'.$curArticle[0]['article_categoryId'].'"';
				$category->query($sql);

				//删除文章和tag关联关系记录
				$tag = M('article_tag');
				$tag->where('article_id='.$delId)->delete();

				//删除文章评论
				$comments = M('Comment');
				$delComResult = $comments->where('comment_articleid='.$delId)->delete();
				if(!$delComResult){
					$errorMsg = '评论删除失败，请手动删除'. $article->getError().'<br/>';
				}
				//删除文章图片
				$articleImg = M('ArticleImg');
				$delImage = $articleImg->query('select * from '.C('DB_PREFIX').'articleImg where article_id='.$delId);
				
				foreach($delImage as $img){
					if(trim($img['path'])!=""){
						$file = dirname(ABSPATH).str_replace('/', '\\',$img['path']);
						if(!unlink($file)){
							$errorMsg = "图片删除失败，请手动删除 路径：".$file.'<br/>';
						}
					}
				}
				//删除数据库记录
				$articleImg->query('delete from '.C('DB_PREFIX').'articleImg where article_id='.$delId);

				if($errorMsg!=""){
					echo 'true';
				}
				else{
					echo $errorMsg;
				}
				return;

			}else{
				echo $article->getError();
			}
			return;
		}
		else{
			$this->error('请先登录', U('/login'));
		}
	}


	/**
	 * 判断用户是否登录
	 */
	public function checkAuthority(){
		if(isset($_SESSION['LoginUser']))
			 return true;
		return false;
	}

}
