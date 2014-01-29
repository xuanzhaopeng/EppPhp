<?php
class SplAction extends Action {
	//display the index page of Spl
	public function index() {
		if (!$this->isLogin())
			$this->error(L('pls_login_first'));	
		
		$this->display();
	}
	
	public function loadSpl() {
		$modelList = D('Spl')->getAllSpl();
		
		if ($modelList === false) {
			header("HTTP/1.0 200 OK");
			jsonecho(0, L('load_data_failed'));
		} else if (empty($modelList)) {
			header("HTTP/1.0 200 OK");
			jsonecho(2, L('no_data'));
		} else {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status => 1, data => $modelList));
		}		
	}
	
	public function isLogin() {
		return  isset($_SESSION[C('ADMIN_AUTH_KEY')]) ? true: false;
	}
	
	public function clearSession() {
		$model = $this->_post('model_id');
		$sessionName = "spl" . $model;
		if(isset($_SESSION["$sessionName"])){
			unset($_SESSION["$sessionName"]);
		}
	}
	
	private function restore_array($arr){
		if (!is_array($arr)){ return $arr; }
		$c = 0; $new = array();
		while (list($key, $value) = each($arr)){
			if (is_array($value)){
				$new[$c] = restore_array($value);
			}
			else { $new[$c] = $value; }
			$c++;
		}
		return $new;
	}
	
	public function printSession(){
		$model = $this->_post('model_id');
		$sessionName = "spl" . $model;
		if(isset($_SESSION["$sessionName"])){
			$list = $_SESSION["$sessionName"];
			echo json_encode(array(status => 1, data => $list));
		}else{
			echo json_encode(array(status => 0));
		}
	}
	
	public function printAll(){
		var_dump($_SESSION);
	}
	
	public function addFeature(){
		$model = $this->_post('model_id');
		$feature = $this->_post('feature_id');
		$sessionName = "spl" . $model;
		if(isset($_SESSION["$sessionName"])){
			$list = $_SESSION["$sessionName"];
			if($list['features'] == null){
				$list['features'] = array();
			}
			$list['features'][] = $feature;
			unset($_SESSION["$sessionName"]);
			$_SESSION["$sessionName"] = $list;
			echo json_encode(array(status => 1, data => $list));
		}else{
			echo json_encode(array(status => 0));
		}
	}
	
	public function removeFeature(){
		$model = $this->_post('model_id');
		$feature = $this->_post('feature_id');
		$sessionName = "spl" . $model;
		if(isset($_SESSION["$sessionName"])){
			$list = $_SESSION["$sessionName"];
			if(($key = array_search($feature, $list['features'])) !== false) {
				unset($list['features'][$key]);
			}
			$list['features'] = restore_array($list['features']);
			unset($_SESSION["$sessionName"]);
			$_SESSION["$sessionName"] = $list;
			echo json_encode(array(status => 1, data => $list));
		}else{
			echo json_encode(array(status => 0));
		}
	}
	
	public function startSelectNode(){
		$node = $this->_post('node_id');
		$model = $this->_post('model_id');
		$sessionName = "spl" . $model;
		$selectedNodes =  $_SESSION["$sessionName"]; 
		//$selectedNodes = array();
		//$node = "Ant";
		//$model = 1;
		$message = "";
		$selectedNodes = $this->selectNode($selectedNodes, $node, $model,$message);
		if($message == ""){
			if(isset($_SESSION["$sessionName"])){
				unset($_SESSION["$sessionName"]);
			}
			$_SESSION["$sessionName"] = $selectedNodes;
			echo json_encode(array(status => 1, data => $selectedNodes));
		}else {
			echo json_encode(array(status => 0, data => $selectedNodes, message => $message));
		}
	}
	
	public function startUnselectNode(){
		$node = $this->_post('node_id');
		$model = $this->_post('model_id');
		$sessionName = "spl" . $model;
		$selectedNodes =  $_SESSION["$sessionName"]; 
		//$selectedNodes = array("Ant","Base","SPL","Team");
		//$node = "Ant";
		//$model = 1;
		$message = "";
		$selectedNodes = $this->unselectNode($selectedNodes, $node, $model,$message);
		if($message == ""){
			if(isset($_SESSION["$sessionName"])){
				unset($_SESSION["$sessionName"]);
			}
			$_SESSION["$sessionName"] = $selectedNodes;
			echo json_encode(array(status => 1, data => $selectedNodes));
		}else {
			echo json_encode(array(status => 0, data => $selectedNodes, message => $message));
		}
	}
	
	public function selectNode($selectedNodes ,$node, $model, &$message) {		
		$Dependency = D('Dependency');
		if($selectedNodes == null){
			$selectedNodes = array();
		}else{
			foreach($selectedNodes as $selectedNode){
				if($selectedNode != $node){
					$map = array();
					$map['model_id'] = $model;
					$map['feature1_id'] = $selectedNode;
					$map['feature2_id'] = $node;
					$item =  $Dependency->where($map)->field(array('dependency'))->select();
					if($item[0]['dependency'] == "NEVER"){
						$message = $message . "$selectedNode+$node+NEVER;";
						return $selectedNodes;
					}	
				}
			}
		}
		$selectedNodes[] = $node;
		
		$map2 = array();
		$map2['model_id'] = $model;
		$map2['feature1_id'] = $node;
		$map2['dependency'] = 'ALWAYS';
		$list2 = $Dependency->where($map2)->field(array('feature2_id'))->select();
		
		foreach($list2 as $item){
			$id = $item['feature2_id'];
			if(($key = array_search($id, $selectedNodes)) === false) {
				$selectedNodes = $this->selectNode($selectedNodes, $id, $model);
			}
		}
		
		return $selectedNodes;
	}
	
	public function unselectNode($selectedNodes,$node, $model, &$message) {
		$Dependency = D('Dependency');
		if($selectedNodes == null || !in_array($node,$selectedNodes )){
			return $selectedNodes;
		}else {
			foreach($selectedNodes as $selectedNode){
				if($selectedNode != $node){
					$map = array();
					$map['model_id'] = $model;
					$map['feature1_id'] = $selectedNode;
					$map['feature2_id'] = $node;
					$item =  $Dependency->where($map)->field(array('dependency'))->select();
					if($item[0]['dependency'] != "MAYBE"){
						$dpp = $item[0]['dependency'];
						$message = $message . "$selectedNode+$node+$dpp;";
						return $selectedNodes;
					}	
				}
			}
			if(($key = array_search($node, $selectedNodes)) !== false) {
				unset($selectedNodes[$key]);
				$selectedNodes = $this->restore_array($selectedNodes);
			}else{
				return $selectedNodes;
			}
			
			$deleteList = array();
			foreach($selectedNodes as $selectedNode){
				$map2 = array();
				$map2['model_id'] = $model;
				$map2['feature1_id'] = $node;
				$map2['feature2_id'] = $selectedNode;
				$item =  $Dependency->where($map)->field(array('dependency'))->select();
				if($item[0]['dependency'] == "ALWAYS"){
					$deleteList[] = $selectedNode;
				}	
			}
			
			foreach($deleteList as $deleteItem){
				$this->unselectNode($selectedNodes,$deleteItem,$model);
			}
			return $selectedNodes;
		}
	}
	
	public function loadEclipse(){
		$repolist = M()->query("SELECT distinct repository FROM `epp_category` WHERE repository like '%http://download.eclipse.org/releases/%'");
		echo json_encode(array(status => 1, data => $repolist));
	}
	
	
	
}