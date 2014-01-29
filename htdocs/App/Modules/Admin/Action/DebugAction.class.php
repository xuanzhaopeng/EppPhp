<?php
class DebugAction extends Action {
	//display the index page of Category
	private $found;

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

	public function loadFeaturePage() {
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
		$list = $Feature->where($map)->distinct(true)->field(array('id','name'))->select();
		
		if ($list === false) {
			header("HTTP/1.0 200 OK");
			jsonecho(0, L('load_data_failed'));
		} else if (empty($list)) {
			header("HTTP/1.0 200 OK");
			jsonecho(2, L('no_data'));
		} else {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status => 1, data => $list));
		}
	}
	

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

	private function recrusiveGroup($value,$groups){
		
		$id = $value['id']; 
		$result = $value;

		if(isset($this->found[$id]))
			return $this->found[$id]['children'];

		foreach ($groups as $group) {
			if($group['parentName'] == $id){
				$result['children'][] = $this->recrusiveGroup($group,$groups); 
			}
		}
		$this->found[$id] = $result;

		if($value['status'] == 0){
			$result['features'] = $this->getGroupFeature($id,$value['model_id']);
		}

		return $result;
	}

	private function sortGroupArray($groups){
		$result =array();
		$this->found = array();

		foreach ($groups as $value) {
			$parent = $value['parentName'];
			$id = $value['id'];

			if($parent == null){
				$result = $value;
				$result = $this->recrusiveGroup($value,$groups);
				break;
			}
		}
		return $result;
	}

	private function getGroupFeature($groupid,$model){
		$query = "SELECT feature_id as id FROM eclipseplusplus.epp_groupfeature WHERE model_id='$model' AND group_id='$groupid'";
		$features = M()->query($query);
		return $features;
	}

	//Load tree
	public function loadTree() {
		//search condition
		$model_id = $this->_post('model');
		$query = "SELECT id as mid,name as mname FROM eclipseplusplus.epp_spl where id = $model_id";
		$model = M()->query($query);
		
		for($i=0;$i<count($model);$i++){
			$query = "SELECT * FROM eclipseplusplus.epp_group WHERE model_id = ".$model[$i]['mid']." order by parentName";
			$model[$i]['group'] = $this->sortGroupArray(M()->query($query));
		}
//var_dump($model);
		if ($model === false) {
			header("HTTP/1.0 200 OK");
			jsonecho(0, L('load_data_failed'));
		} else if (empty($model)) {
			header("HTTP/1.0 200 OK");
			jsonecho(2, L('no_data'));
		} else {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status => 1, data => $model));
		}
	}

	public function getGroupInfo() {
		$id = $this->_post('id');
		//echo $id;
		$group = D('Group')->getGroupInfoById($id);

		$group['features'] = M()->query("SELECT distinct(id) as id,name FROM eclipseplusplus.epp_feature");
		$target = M()->query("SELECT feature_id FROM eclipseplusplus.epp_groupfeature WHERE group_id=$id");

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
			echo json_encode(array(status=>1, data=>$group));
		}
	}

	public function isLogin() {
		return  isset($_SESSION[C('ADMIN_AUTH_KEY')]) ? true: false;
	}
	
	public function getFeatureInfo() {
		$id = $this->_post('id');
		//echo $id;
		$feature = D('Feature')->getFeatureInfoById($id);
		//print_r($agency);
		if (!empty($feature)) {
			header("HTTP/1.0 200 OK");
			echo json_encode(array(status=>1, data=>$feature));
		}
	}
	
	public function doBuild(){
		$model_id =$_POST['model_id'];
		$sessionName = "spl" . $model_id;
		$groups = $_SESSION["$sessionName"];
		$map['id']=$model_id;
		$features = array();
		$repo = array();

		$model_repo = D('Spl')->where($map)->field("repo_url")->select();

		foreach ($groups as $value) {
			$features[] = $this->getFeaturesByGroup($value,$model_id);
		}

		$features = array_unique( $this->cleanArray($features,'feature_id') );

		foreach ($features as $value) {
			$repo[] = $this->getRepoByFeature($value,$model_repo[0]['repo_url']);
		} 
		
		$tempArray = $this->cleanArray($repo,'repo_url');
		$model_repo_url = $model_repo[0]['repo_url'];
		$tempArray[] = $model_repo_url;

		$data['features'] = array_values($features);
		$data['repo'] = array_values(array_unique( $tempArray )); 
		$data['email'] = $_SESSION["email"];
		$data['groups'] = array_values($groups);
		echo json_encode(array(data=>$data));
		
		//$this->request_by_curl('http://127.0.0.1:8078/EppServer/MyServlet', $data);
		
		//$this->success("Create OK!", "/Admin/Debug/index"); 
	}

	private function getFeaturesByGroup($gid,$mid){
		$features = M()->query("SELECT feature_id FROM eclipseplusplus.epp_groupfeature WHERE group_id = '$gid' AND model_id = '$mid'");
		return $features;
	}

	private function getRepoByFeature($fid,$repo){
		if(stripos($fid, "org.eclipse") !== false)
		{
			$temp = M()->query("SELECT repo_url FROM eclipseplusplus.epp_featurerepo WHERE feature_id = '$fid' AND repo_url = '$repo'");
			return $temp;
		}
		else
		{
			$temp = M()->query("SELECT repo_url FROM eclipseplusplus.epp_featurerepo WHERE feature_id = '$fid'");
			return $temp;
		}
	} 

	private function cleanArray($array, $target){
		$result=array();
		foreach ($array as $key => $value) {
			if(is_array($value)){
				$result = array_merge($this->cleanArray($value, $target),$result);
			}elseif($key == $target){
				$result[] = $value;
			}
		}
		return $result;
	}

	private function request_by_curl($remote_server, $post_array) {
		$data_string = json_encode($post_array);                                                                                   
 
		$ch = curl_init($remote_server);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      
		curl_setopt($ch, CURLOPT_HEADER, TRUE);		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json',                                                                                
				'Content-Length: ' . strlen($data_string))                                                                       
		);                                                                                                                   
		$result = curl_exec($ch);
		curl_close($ch);
	}
}