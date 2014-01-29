<?php
class CategoryAction extends Action {
	//display the index page of Category
	public function index() {
		if (!$this->isLogin())
			$this->error(L('pls_login_first'));	
		
		$this->display();
	}
	
	public function loadCategory() {
		$categoryList = D('Category')->getCategoryBriefList();
		
		if ($categoryList === false) {
			header("HTTP/1.0 200 OK");
			jsonecho(0, L('load_data_failed'));
		} else if (empty($categoryList)) {
			header("HTTP/1.0 200 OK");
			jsonecho(2, L('no_data'));
		} else {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status => 1, data => $categoryList));
		}		
	}
	
	public function loadCategoryPage() {
		$nowPage = isset($_POST['p'])?$_POST['p']:1;
		//search condition
		$sName = isset($_POST['sName'])?$_POST['sName']:'';

		$map = array();
		if (!empty($sName)) {
			$map['name'] = array('like', "%$sName%");
			$map['repository'] = array('like', "%$sName%");
			$map['_logic'] = 'OR';
		}
		$Category = D('Category');
		$count = $Category->where($map)->getCount();
		
		import('ORG.Util.Page');
		$Page = new Page($count, 15, '/Admin/Category/loadCategoryPage');
		
		$list = $Category->where($map)->page($nowPage.','.$Page->listRows)->select();

		$totalPages = $Page->getTotalPages();
		$pageInfo=array(totalPages => $totalPages, nowPage => $nowPage);
		//print_r($pageInfo);
		if ($list === false) {
			header("HTTP/1.0 200 OK");
			jsonecho(0, L('load_data_failed'));
		} else if (empty($list)) {
			header("HTTP/1.0 200 OK");
			jsonecho(2, L('no_data'));
		} else {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status => 1, data => $list, page => $pageInfo));
		}
	}
	
	public function isLogin() {
		return  isset($_SESSION[C('ADMIN_AUTH_KEY')]) ? true: false;
	}
	
	public function getCategoryInfo() {
		$name = $this->_post('name');
		//echo $id;
		$category = D('Category')->getCategoryInfoByName($name);
		//print_r($agency);
		if (!empty($category)) {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status=>1, data=>$category));
		}
	}
}