<?php
class GroupAction extends Action {
	//display the index page of Groups
	private $grouparray;

	public function index() {
		if (!$this->isLogin())
			$this->error(L('pls_login_first'));	
		
		$this->display();
	}
	
	public function uploadModel() {
		import('ORG.Net.UploadFile');
		$config['savePath'] = './Uploads/';
		$config['maxSize'] = '104857600'; //100M
		$config['allowExts'] = array('xml');
		$upload = new UploadFile($config);
		if(!$upload->upload()) {
			header("HTTP/1.0 200 OK");
			jsonecho(0, L('import_failed'));
		}
		$info = $upload->getUploadFileInfo();
		
		$filetmpname = './Uploads/'.$info[0]['savename'];
		echo json_encode(array(status => 1, filename => $filetmpname));
	}
	/**
		Add access of HTML


	**/
	public function doAdd() {
		$model_path = $this->_post('modelfile');
		$dependency_path = $this->_post('dependencyfile');
		$name = $_POST['name'];
		$eclipse = $_POST['eclipseselect'];
		
		//create model
		$model = D('Spl');
		if (!$model->create()) {
			$this->error($model->getError(), __URL__."/add");
		}
		$data['name'] = $name;
		$data['repo_url'] = $eclipse;
		$model_id = $model->add($data);
		
		if ($model_id > 0) {
			$this->doImport($model_path,$model_id);
			$this->updateDependency($dependency_path,$model_id);
			$this->success($model_id, "/Admin/Group/index");
		} else {
			$this->error(L('add_failed'), __URL__+"/add");
		}
	}


	/**
		Start of Import


	**/
	public function doImport($path,$model_id){ 
		$this->grouparray=array();
		$this->readXML($path,$model_id);
	}

	/**
		Update access of HTML // Start of Update


	**/
	public function doUpdate(){
		
		$model_path = $this->_post('modelfile');
		$dependency_path = $this->_post('dependencyfile'); 
		$model_id = $this->_post('modelid');

		if ($model_id > 0) {
			$this->updateFromXML($model_path,$model_id);
			$this->updateDependency($dependency_path,$model_id);
			$this->success($model_id, "/Admin/Group/index");
		} else {
			$this->error(L('update_failed'), __URL__+"/update");
		} 
	}

	/**
		Upload/Update Dependence


	**/
	public function uploadDependency() {
		import('ORG.Net.UploadFile');
		$config['savePath'] = './Uploads/';
		$config['maxSize'] = '104857600'; //100M
		$config['allowExts'] = array('txt');
		$upload = new UploadFile($config);
		if(!$upload->upload()) {
			header("HTTP/1.0 200 OK");
			jsonecho(0, L('import_failed'));
		}
		$info = $upload->getUploadFileInfo();
		
		$filetmpname = './Uploads/'.$info[0]['savename'];
		echo json_encode(array(status => 1, filename => $filetmpname));
	}
	
	public function updateDependency($path,$model){
		$this->clearDependency($model);
		$this->importDependency($path,$model);
	}

	private function clearDependency($model){
		M()->query("DELETE FROM eclipseplusplus.epp_dependency where model_id='$model'");
	}
	
	private function importDependency($path,$model){
		$handle = fopen($path, "r");
		
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				if (strpos($line,':=') === false) {
					$temp = explode(" ", $line);
					if(sizeof($temp) == 3){
						$data['feature1_id'] = trim($temp[0]);
						$data['feature2_id'] = trim($temp[2]);
						$data['dependency'] = trim($temp[1]);
						$data['model_id'] = $model;
						
						D('Dependency')->add($data);
					}
				}
			}
		} else {
			echo "Not such dependency file\n";
		}
	
	}
	 
	/**
		Read model.xml and insert into db


	**/
	private function readXML($path,$model_id){
		$xmls = simplexml_load_file($path);

		foreach ($xmls->children() as $xml)
		{
		  if($xml->getName() === "struct"){
		  	$nodes = $xml->children();
			$this->readXMLGroup($nodes[0],"",true,$model_id);
			break;
		  }
		}
	}

	private function readXMLGroup($xml,$parent,$write=true,$model_id){
	 	
	 	if($xml->getName() == "and" || $xml->getName() == "or" || $xml->getName() == "alt")
		{	
			$status = $this->getAttribute($xml,"abstract");
			$name = $this->getAttribute($xml,"name");
			$this->insertGroup($name,$parent,$status,$write,$model_id);
			
			foreach ($xml->children() as $node) {
				$this->readXMLGroup($node,$name,true,$model_id);
			}
		}
		elseif($xml->getName() == "feature")
		{
			$status = $this->getAttribute($xml,"abstract");
			$name = $this->getAttribute($xml,"name");
			$this->insertGroup($name,$parent,$status,$write,$model_id);	
		}
	}  

	/**
		Update model.xml and db


	**/
	private function updateFromXML($path,$model_id){
		$xmls = simplexml_load_file($path);
		$group = D('Group');
		$map = array();
		$map['model_id'] = $model_id;
		$list = $group->where($map)->select();
		
		foreach ($xmls->children() as $xml)
		{
		  if($xml->getName() === "struct"){
		  	$nodes = $xml->children();
			$this->readXMLGroup($nodes[0],"",false,$model_id);
			break;
		  }
		}

		$this->addNewGroup($this->grouparray,$list);
		$this->deleteLostGroup($this->grouparray,$list);
		$this->modifyGroup($this->grouparray,$list);

	}	
	/**
		Small functions for Update from model.xml


	**/
	private function addNewGroup($newarray,$oldarray){
		foreach ($newarray as $new) {
			//Not in old array, so insert it
			if($this->arraySearch("id",$new["id"], $oldarray) == false){
				$this->insertGroupIntoDB($new);
			}
		}
	}

	private function deleteLostGroup($newarray,$oldarray){
		foreach ($oldarray as $old) {
			//Not in new array, so delete
			if($this->arraySearch("id",$old["id"],$newarray) == false){ 
				$this->deleteGroupFromDB($old);
				$this->deleteGroupFeature($old["id"],$old["model_id"]);
			}
		}
	}

	private function modifyGroup($newarray,$oldarray){
		foreach ($newarray as $new) {
			if(($key=$this->arraySearch("id",$new["id"], $oldarray)) != false){
				
				if($key['parentName'] != $new['parentName']){
					$this->alterGroup($new,"parentName");	
				}
				if($key['status'] != $new['status']){
					$this->alterGroup($new,"status");	
				}
			}
		}
	}

	private function arraySearch($key,$value,$array){
		foreach ($array as $unit) { 
			if($unit[$key] == $value){ 
				return $unit;
			}
		}
		return false;
	}

	private function getAttribute($xml,$attribute){

		foreach ($xml->attributes() as $key=>$attr) {
			if($key === $attribute)
			{
				return strval($attr);
			}
		}
		return null;
	}

	private function insertGroup($name,$parent,$status,$write,$model_id){
		$data['id'] = $name;
		$data['name'] = $name;
		$data['description'] = "";
		$data['parentName'] = $parent;
		$data['model_id'] = $model_id;
		$data['status'] = $status == true ? 1:0; 

		$this->grouparray[] = $data;
		if($write){
			$this->insertGroupIntoDB($data);
		}
	}

	private function insertGroupIntoDB($data){
		$group = D('Group');
		$group->add($data);		
	}

	private function deleteGroupFromDB($data){ 
		$query = "DELETE FROM eclipseplusplus.epp_group WHERE id='".$data["id"]."'";
		M()->query($query);
	}

	private function alterGroup($data,$field){

		$query = "UPDATE eclipseplusplus.epp_group SET $field='".$data[$field]."' WHERE id='".$data['id']."'";
		M()->query($query);
	}

	/**
		END


	**/
	public function loadGroup() {
		$groupList = D('Group')->getGroupBriefList();
		
		if ($groupList === false) {
			header("HTTP/1.0 200 OK");
			jsonecho(0, L('load_data_failed'));
		} else if (empty($groupList)) {
			header("HTTP/1.0 200 OK");
			jsonecho(2, L('no_data'));
		} else {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status => 1, data => $groupList));
		}		
	}
	
	
	public function loadGroupPage() { 
		$model_id = $_POST['model'];
		$nowPage = isset($_POST['p'])?$_POST['p']:1;
		//search condition
		$sName = isset($_POST['sName'])?$_POST['sName']:''; 
		$map = array();
		$map['model_id'] = $model_id;
		if (!empty($sName)) {
			$map['id'] = array('like', "%$sName%");
		} 

		$group = D('Group');
		$count = $group->where($map)->getCount();
		
		import('ORG.Util.Page');
		$Page = new Page($count, 15, '/Admin/Group/loadGroupPage');

		$list = $group->where($map)->page($nowPage.','.$Page->listRows)->select();
		
		foreach( $list as &$groupItem ){
			$repoSql = 'select distinct repo_url as repo from epp_featurerepo where ';
			$groupId = $groupItem['id'];
			$group_featureList = M()->query("select feature_id as id from epp_groupfeature where group_id = '$groupId' and model_id = $model_id");
			$groupItem['feature'] = $group_featureList;
			$groupItem['repo'] = array();
			foreach ($group_featureList as $featureItem){
				$featureId = $featureItem['id'];
				$repoSql = $repoSql . "feature_id = '$featureId' or " ;
			}
			$repoSql = $repoSql . 'false';
			$groupItem['repo'] = M()->query($repoSql);
		}
		
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
			echo json_encode(array(status => 1, data => $list, page => $pageInfo, debug => $repoSql));
		}
	}
	
	
	public function isLogin() {
		return  isset($_SESSION[C('ADMIN_AUTH_KEY')]) ? true: false;
	}
	
	public function getSelectedFeaturesPage(){
		$model_id = $this->_post('model');
		$id = $this->_post('id');
		$nowPage = isset($_POST['p'])?$_POST['p']:1;
		
		$map = array();
		$map['model_id'] = $model_id;
		$map['id'] = $id;
		$map['_logic'] = 'AND';
		$Groups = D('Group');
		$group = $Groups->where($map)->select();
		//$group = D('Group')->getGroupInfoById($id);
		
		$countResult = M()->query("select count(*) from (select t2.id,t2.name from (select feature_id from epp_group left join epp_groupfeature on epp_group.id = epp_groupfeature.group_id and epp_group.model_id = epp_groupfeature.model_id where epp_group.id = '$id' and epp_group.model_id = $model_id  ) as t1 left join (select id,name from epp_feature group by id) as t2 on t1.feature_id = t2.id) as t3");
		$count = $countResult[0]["count"];
		
		import('ORG.Util.Page');
		$Page = new Page($count, 15, '/Admin/Group/getSelectedFeaturesPage');
		
		$limit = ($nowPage - 1) * 15;
		
		$group['features'] = M()->query("select t2.id,t2.name from (select feature_id  from epp_group left join epp_groupfeature on epp_group.id = epp_groupfeature.group_id and epp_group.model_id = epp_groupfeature.model_id where epp_group.id = '$id' and epp_group.model_id = $model_id  ) as t1 left join (select id,name from epp_feature group by id) as t2 on t1.feature_id = t2.id limit {$limit},{$Page->listRows}");

		$totalPages = $Page->getTotalPages();
		$pageInfo=array(totalPages => $totalPages, nowPage => $nowPage);
		
		$target = M()->query("SELECT feature_id FROM eclipseplusplus.epp_groupfeature WHERE group_id='$id' and model_id=$model_id ");

		for ($i=0; $i <count($group['features']) ; $i++) { 	
			$group['features'][$i]['contained'] = true;
		}
		
		if (!empty($group)) {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status=>1, data=>$group, page => $pageInfo ));
		}
	}
		
	private function endsWith($haystack, $needle)
	{
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
	
	
	
	
	public function getGroupPage() {
		$model_id = $this->_post('model');
		$id = $this->_post('id');
		$searchFeatureId = $this->_post('fId');
		
		$SPL = D('Spl');
		$mapspl = array();
		$mapspl['id'] = $model_id;
		$spls = $SPL->where($mapspl)->select();
		$repo_url = $spls[0]["repo_url"];
		
		$basicLength = strlen("http://download.eclipse.org/releases/");
		$eclipseVersion = substr($repo_url,$basicLength);
		if($this->endsWith($eclipseVersion,'/')){
			$versionLength = strlen($eclipseVersion);
			$eclipseVersion = substr($eclipseVersion,0,$versionLength - 1);
		}
				
		$nowPage = isset($_POST['p'])?$_POST['p']:1;
		//echo $id;
		$map = array();
		$map['model_id'] = $model_id;
		$map['id'] = $id;
		$Groups = D('Group');
		$group = $Groups->where($map)->select();
		//$group = D('Group')->getGroupInfoById($id);
	

		$limit = ($nowPage - 1) * 15;
		
		if($searchFeatureId!=null && $searchFeatureId != ''){
			$countResult = M()->query("select count(*) as count from (SELECT * FROM epp_feature left join epp_featurerepo on epp_feature.id = epp_featurerepo.feature_id where (epp_featurerepo.repo_url like '%$eclipseVersion%' or epp_featurerepo.repo_url not like '%http://download.eclipse.org/releases%' ) and epp_feature.id like '%$searchFeatureId%' group by id) as t1;");
			$count = $countResult[0]["count"];
		
			import('ORG.Util.Page');
			$Page = new Page($count, 15, '/Admin/Group/getGroupPage');
		
			$group['features'] = M()->query("SELECT id,name FROM epp_feature left join epp_featurerepo on epp_feature.id = epp_featurerepo.feature_id where (epp_featurerepo.repo_url like '%$eclipseVersion%' or epp_featurerepo.repo_url not like '%http://download.eclipse.org/releases%' ) and epp_feature.id like '%$searchFeatureId%' group by id limit {$limit},{$Page->listRows}");

			$totalPages = $Page->getTotalPages();
			$pageInfo=array(totalPages => $totalPages, nowPage => $nowPage);
		
			$target = M()->query("SELECT feature_id FROM eclipseplusplus.epp_groupfeature WHERE group_id='$id' and model_id=$model_id and feature_id like '%$searchFeatureId%'");
		

			for ($i=0; $i <count($group['features']) ; $i++) { 	
				foreach ($target as $t) {
					if($group['features'][$i]['id'] == $t['feature_id']){
						$group['features'][$i]['contained'] = true;
						break;
					}
				}
				if(!isset($group['features'][$i]['contained'])){
					$group['features'][$i]['contained'] = false;
				}
			}
			
			if (!empty($group)) {
				header("HTTP/1.0 200 OK");
				echo json_encode(array(status=>1, data=>$group, page => $pageInfo ));
			}
		}else{
			$countResult = M()->query("select count(*) as count from (SELECT id,name FROM epp_feature left join epp_featurerepo on epp_feature.id = epp_featurerepo.feature_id where epp_featurerepo.repo_url like '%$eclipseVersion%' or epp_featurerepo.repo_url not like '%http://download.eclipse.org/releases%' group by id ) as t1;");
			$count = $countResult[0]["count"];
		
			import('ORG.Util.Page');
			$Page = new Page($count, 15, '/Admin/Group/getGroupPage');
		
			$group['features'] = M()->query("SELECT id,name FROM epp_feature left join epp_featurerepo on epp_feature.id = epp_featurerepo.feature_id where epp_featurerepo.repo_url like '%$eclipseVersion%' or epp_featurerepo.repo_url not like '%http://download.eclipse.org/releases%' GROUP BY id limit {$limit},{$Page->listRows}");

			$totalPages = $Page->getTotalPages();
			$pageInfo=array(totalPages => $totalPages, nowPage => $nowPage);
		
			$target = M()->query("SELECT feature_id FROM eclipseplusplus.epp_groupfeature WHERE group_id='$id' and model_id=$model_id");
		

			for ($i=0; $i <count($group['features']) ; $i++) { 	
				foreach ($target as $t) {
					if($group['features'][$i]['id'] == $t['feature_id']){
						$group['features'][$i]['contained'] = true;
						break;
					}
				}
				if(!isset($group['features'][$i]['contained'])){
					$group['features'][$i]['contained'] = false;
				}
			}
			
			if (!empty($group)) {
				header("HTTP/1.0 200 OK");
				echo json_encode(array(status=>1, eclipse=>$eclipseVersion, data=>$group, page => $pageInfo ));
			}
		}

	}
	
	
	public function getGroupInfo() {
		$id = $this->_post('id');
		//echo $id;
		$group = D('Group')->getGroupInfoById($id);
		
		if (!empty($group)) {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status=>1, data=>$group));
		}
	}
	 
	public function add() {
		$this->display();
	}
	
	
	private function doGroupFeature($gid,$model,$features){
			
		foreach ($features as $feature) {
			$query = "INSERT IGNORE INTO eclipseplusplus.epp_groupfeature VALUES('$gid',$model,'$feature')";
			M()->query($query);
		}
	}

	private function deleteGroupFeature($gid,$model){
		$query = "DELETE FROM eclipseplusplus.epp_groupfeature WHERE group_id='$gid' and model_id=$model";
		M()->query($query);
	}

	private function editGroupFeature($gid,$model,$features){
		$this->deleteGroupFeature($gid,$model);
		$this->doGroupFeature($gid,$model,$features);
	}

	public function doAjaxEditFeature() {
		$groupId = $this->_post('groupId');
		if (!isset($groupId)) {
			header("HTTP/1.0 200 OK");
			jsonecho (3, L('edit_failed'));
		}
		
		$model_id = $this->_post('model');
		
		$type = $this->_post('type');
		if (!isset($groupId)) {
			header("HTTP/1.0 200 OK");
			jsonecho (3, L('edit_failed'));
		}
		
		$feature = $this->_post('feature');

		if($type == 'add'){
			$query = "INSERT IGNORE INTO eclipseplusplus.epp_groupfeature VALUES('$groupId',$model_id,'$feature')";
			M()->query($query);
		}else if($type == 'delete') {
			$query = "DELETE FROM eclipseplusplus.epp_groupfeature WHERE group_id = '$groupId' and model_id = $model_id and feature_id='$feature'";
			M()->query($query);
		}

		header("HTTP/1.0 200 OK");
		echo json_encode(array(groupId=>$groupId, feature=>$feature));
	}

	public function doEdit() {
		$model_id = $this->_post('modelId');
	
		$name = $this->_post('name');
		if(empty($name))  {
			header("HTTP/1.0 200 OK");
			jsonecho(1,L('name_require'));
		}
		 
		$group = D('Group');
		if (!$group->create()) {
			$errorMsg = $Group->getError();
			header("HTTP/1.0 200 OK");
			jsonecho(2, L($errorMsg));
		}
		
		$groupId = $this->_post('groupId');
		if (!isset($groupId)) {
			header("HTTP/1.0 200 OK");
			jsonecho (3, L('edit_failed'));
		}
		
		$name = $this->_post('name');
		
		$data['model_id'] = $model_id;
		$data['id'] = $groupId;
		$data['name'] = $this->_post('name');
		$data['description'] = $this->_post('description');
		
		$features = $this->_post('features');

		$condition['id'] = $data['id'];
		$condition['model_id'] = $data['model_id'];
		$result = $group->where($condition)->save($data);
		if ($result === false) {
			header("HTTP/1.0 200 OK");
			jsonecho(5, L('edit_failed'));
		}
		
		//$this->editGroupFeature($data['id'],$data['model_id'],$features);

		header("HTTP/1.0 200 OK");
		jsonecho(0);
	}
	
	
	function doDelete() {
		$ids = $_POST['ids'];

		$result = D('Group')->deleteGroupsByIds($ids);
		if ($result === false) {
			header("HTTP/1.0 200 OK");
			jsonecho(0, L('delete_failed'));
		} else {
			foreach ($ids as $id ) {
				$this->deleteGroupFeature($id);
			}
			header("HTTP/1.0 200 OK");
			jsonecho(1);
		}
	}
	
	public function getTree() {
		$model_id = $this->_post('model');
		$tree = array();
		$tree = $this->getTreeMethod($tree,'',$model_id);
		echo json_encode($tree);
	}
	
	public function getTreeMethod($tree,$name,$model_id) {
		if($name == ''){
			$tree = M()->query("select id,name,status from epp_group where model_id = $model_id and parentName = '' ");
		}else {
			$tree = M()->query("select id,name,status  from epp_group where model_id = $model_id and parentName = '$name' ");
		}	
		foreach ($tree as &$node)
		{
			$name = $node['name'];
			$children = M()->query("select id,name,status from epp_group where model_id = $model_id and parentName = '$name'");
			if($children != null && count($children) > 0){
				$node['children'] = array();
				$node['children'] = $this->getTreeMethod($node['children'], $name, $model_id);
			}
		}
		return $tree;
	}
	
	public function getDependency(){
		$model_id = $this->_post('model');
		$group_id = $this->_post('group');
		$result = M()->query("select feature2_id as id from epp_dependency where model_id = $model_id and feature1_id = '$group_id' and dependency = 'ALWAYS'");
		echo json_encode($result);
	}
}