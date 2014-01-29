<?php
class FeatureAction extends Action {
	//display the index page of Category
	public function index() {
		if (!$this->isLogin())
			$this->error(L('pls_login_first'));	
		
		$this->display();
	}
	
	public function loadFeature() {
		$featureList = D('Feature')->getFeatureBriefList();
		
		if ($featureList === false) {
			header("HTTP/1.0 200 OK");
			jsonecho(0, L('load_data_failed'));
		} else if (empty($featureList)) {
			header("HTTP/1.0 200 OK");
			jsonecho(2, L('no_data'));
		} else {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status => 1, data => $featureList));
		}		
	}
	
	public function loadDistinctFeaturePage() {
		$nowPage = isset($_POST['p'])?$_POST['p']:1;
		//search condition
		$sName = isset($_POST['sName'])?$_POST['sName']:'';

		$map = array();
		if (!empty($sName)) {
			$map['id'] = array('like', "%$sName%");
			$map['name'] = array('like', "%$sName%");
			$map['publisher'] = array('like', "%$sName%");
			$map['_logic'] = 'OR';
		}
		$Feature = D('Feature');
		$count = $Feature->where($map)->count('distinct(id)');
		
		import('ORG.Util.Page');
		$Page = new Page($count, 15, '/Admin/Feature/loadFeaturePage');
		
		$list = $Feature->where($map)->distinct(true)->page($nowPage.','.$Page->listRows)->field(array('id','name'))->select();

		$totalPages = $Page->getTotalPages();
		$pageInfo=array(totalPages => $totalPages, nowPage => $nowPage);
		
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
	
	public function loadFeaturePage() {
		$nowPage = isset($_POST['p'])?$_POST['p']:1;
		//search condition
		$sName = isset($_POST['sName'])?$_POST['sName']:'';

		$map = array();
		if (!empty($sName)) {
			$map['id'] = array('like', "%$sName%");
			$map['name'] = array('like', "%$sName%");
			$map['publisher'] = array('like', "%$sName%");
			$map['_logic'] = 'OR';
		}
		$Feature = D('Feature');
		$count = $Feature->where($map)->getCount();
		
		import('ORG.Util.Page');
		$Page = new Page($count, 15, '/Admin/Feature/loadFeaturePage');
		
		$list = $Feature->where($map)->page($nowPage.','.$Page->listRows)->select();

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
	
	public function loadFeatureByCategoryPage() {
		$name = $this->_post('name');
		$nowPage = isset($_POST['p'])?$_POST['p']:1;
		//echo $id;
		
		$countResult = M()->query("select count(*) as count from (select * from epp_feature left join epp_categoryfeature on epp_feature.id = epp_categoryfeature.feature_id where epp_categoryfeature.category_id = '$name') as t1;");
		$count = $countResult[0]["count"];
		
		import('ORG.Util.Page');
		$Page = new Page($count, 15, '/Admin/Feature/loadFeatureByCategoryPage');
		
		$limit = ($nowPage - 1) * 15;
		
		$list = M()->query("select * from epp_feature left join epp_categoryfeature on epp_feature.id = epp_categoryfeature.feature_id where epp_categoryfeature.category_id = '$name' limit {$limit},{$Page->listRows}");

		$totalPages = $Page->getTotalPages();
		$pageInfo=array(totalPages => $totalPages, nowPage => $nowPage);
		
		if (!empty($list)) {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status=>1, data=>$list, page => $pageInfo ));
		}
	}
	
	public function loadDistinctFeatureByCategoryPage() {
		$name = $this->_post('name');
		$nowPage = isset($_POST['p'])?$_POST['p']:1;
		//echo $id;
		
		$countResult = M()->query("select count(*) as count from (select * from epp_feature left join epp_categoryfeature on epp_feature.id = epp_categoryfeature.feature_id where epp_categoryfeature.category_id = '$name' group by epp_feature.id) as t1;");
		$count = $countResult[0]["count"];
		
		import('ORG.Util.Page');
		$Page = new Page($count, 15, '/Admin/Feature/loadFeatureByCategoryPage');
		
		$limit = ($nowPage - 1) * 15;
		
		$list = M()->query("select * from epp_feature left join epp_categoryfeature on epp_feature.id = epp_categoryfeature.feature_id where epp_categoryfeature.category_id = '$name' group by epp_feature.id limit {$limit},{$Page->listRows}");

		$totalPages = $Page->getTotalPages();
		$pageInfo=array(totalPages => $totalPages, nowPage => $nowPage);
		
		if (!empty($list)) {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status=>1, data=>$list, page => $pageInfo ));
		}
	}
	
	public function loadRepositoryPage(){
		$id = $this->_post('id');
		$nowPage = isset($_POST['p'])?$_POST['p']:1;
		//echo $id;
		
		$countResult = M()->query("select count(*) as count from (select * from epp_featurerepo where feature_id = '$id') as t1;");
		$count = $countResult[0]["count"];
		
		import('ORG.Util.Page');
		$Page = new Page($count, 15, '/Admin/Feature/loadRepositoryPage');
		
		$limit = ($nowPage - 1) * 15;
		
		$list = M()->query("select repo_url as url from epp_featurerepo where feature_id = '$id' limit {$limit},{$Page->listRows}");

		$totalPages = $Page->getTotalPages();
		$pageInfo=array(totalPages => $totalPages, nowPage => $nowPage);
		
		if (!empty($list)) {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status=>1, data=>$list, page => $pageInfo ));
		}
	}
	
	public function isLogin() {
		return  isset($_SESSION[C('ADMIN_AUTH_KEY')]) ? true: false;
	}
	
	public function getFeatureInfo() {
		$id = $this->_post('id');
		//echo $id;
		$list = M()->query("select * from epp_feature where epp_feature.id = '$id' group by epp_feature.id;");
		//print_r($agency);
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status=>1, data=>$list ));
		
	}
}