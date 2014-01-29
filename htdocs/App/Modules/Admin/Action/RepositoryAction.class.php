<?php
class RepositoryAction extends Action {
	//display the index page of Category
	public function index() {
		if (!$this->isLogin())
			$this->error(L('pls_login_first'));	
		
		$this->display();
	}
	
	public function loadRepositoryPage() {
		$nowPage = isset($_POST['p'])?$_POST['p']:1;
		//search condition
		$sName = isset($_POST['sName'])?$_POST['sName']:'';

		$map = array();
		if (!empty($sName)) {
			$map['repository'] = array('like', "%$sName%");
		}
		
		$Category = D('Category');
		$count = $Category->where($map)->count('distinct(repository)');
		
		import('ORG.Util.Page');
		$Page = new Page($count, 15, '/Admin/Repository/loadRepositoryPage');
		
		$list = $Category->where($map)->distinct(true)->page($nowPage.','.$Page->listRows)->field(array('repository'))->select();

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
	
	public function doAdd() {
		$repository = $_POST['repository'];	
		$data = array('repository'=>$repository);
        $post_string = http_build_query($data);
		$this->request_by_curl('http://127.0.0.1:8078/EppServer/RepositoryServlet', $post_string);
		
		$this->success(L('add_success'), "/Admin/Repository/index");
	}
	
	
	/**
 * Curl版本
 * 使用方法：
 * $post_string = "app=request&version=beta";
 * request_by_curl('http://www.qianyunlai.com/restServer.php', $post_string);
 */
	public function request_by_curl($remote_server, $post_string) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $remote_server);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$data = curl_exec($ch);
			curl_close($ch);
	}
	
	public function isLogin() {
		return  isset($_SESSION[C('ADMIN_AUTH_KEY')]) ? true: false;
	}
}