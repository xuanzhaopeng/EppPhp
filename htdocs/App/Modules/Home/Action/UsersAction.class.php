<?php
header("content-Type: text/html; charset=utf-8"); 


class UsersAction extends Action{
	public function index() {
		$uid = $_SESSION[C('USER_AUTH_KEY')];
		if (!isset($uid)) {
			$this->error(L('pls_login_first'));
		}
		
		$Users = D('Users');
		$userInfo = $Users->getBasicUserInfoByUid($uid);

		//don't display [Retour] button, when login from /Users/index
		$this->assign('hideRetourBtn', '1');
		if (!empty($userInfo)) {
			//include("Mobile_Detect.php");
			$detect = new Mobile_Detect();
			if ($detect->isMobile()) {
				$this->_view($userInfo, 'life');
			} else {
				$this->_view($userInfo, 'home');
			}
		} else {
			$this->error(L('user_not_exist'), '/Users/login');
		}
	}
		
	public function login() {
		if(isset($_SESSION[C('USER_AUTH_KEY')])){
			$encryptid = encryptUid($_SESSION[C('USER_AUTH_KEY')]);
			$this->redirect2('/Users/userProfile?uid='.$encryptid);
		}else {
			header('HTTP/1.1 200 OK');
			$this->display();
		}
	}
	
	//login check
	public function checkUser() {
		$U = D('Users');	
		$email = trim($this->_post('email'));
		$account = trim($this->_post('account'));
//		$user = $U->getBasicUserInfoByEmail($email);
		$user = $U->getBasicUserInfoByAccount($account);

		if(false === $user) {
			$this->error(L('login_failed'));
		}
		
		if (null == $user) {
			$this->error(L('wrong_email'));
		}

		if($user['password'] != md5($this->_post('password'))) {
			$this->error(L('wrong_password'));
		}
		
		if($user['confirmid'] == 0 && $user['createdby'] == 1) {
			$this->error(L('login_failed_hasnot_confirmed'));
		}
		//This user was created by agency that must be confirmed.
		//or it was created by user   	
		$_SESSION[C('USER_AUTH_KEY')] =	$user['uid'];
		$_SESSION['clientname'] = $user['account'];
		unset($_SESSION[C('AGENCY_AUTH_KEY')]); 
		$_SESSION[C('USER_GROUP')] = 'common';
		$_SESSION['account'] = $user['account'];
		$_SESSION['email'] = $user['email'];
		$_SESSION['opt'] = 'user_opt';

		//save password to cookie
		$remember = $this->_post('remember');
		$password = $this->_post('password');
		if ($remember == 1) {
			setcookie('user_account',$account,time()+3600);
			setcookie('user_email',$email,time()+3600);
			setcookie('user_pwd',$password,time()+3600);
			setcookie('user_remember',$remember,time()+3600);
		} else {
			setcookie('user_account',$account,time()-3600);
			setcookie('user_email',$name,time()-3600);
			setcookie('user_pwd',$password,time()-3600);
			setcookie('user_remember',$remember,time()-3600);
		}
		
		//$encryptid = encryptUid($user['uid']);
		$this->redirect2('/Users/index?uid='.$encryptid.'&opt=user_opt');
	}
	
	public function createUser() {
		if (!$this->islogin()) {
			$this->assign('userGroup', $_SESSION[C('USER_GROUP')]);
			header('HTTP/1.1 200 OK');
			$this->display();
		} else {
			$this->redirectedToLoginPage();
		}
	}
	
	public function doUserSearch(){
		$searchKey = $_GET['key'];
		
	}
	
	
	public function userProfile() {
		if (!$this->islogin()) {
			$this->redirectedToLoginPage();
		}
		$encryptid = $_GET['uid'];
		$uid = decryptUid($encryptid);
		
		if (!empty($uid)) {
			$this->assign('uid', $uid);
		}
		$opt = $_SESSION['opt'];

		$this->assign('opt', $opt);
		$hasConfirmed = D('Users')->hasConfirmed($uid);
		$this->assign('hasConfirmed', $hasConfirmed);
		$this->assign('userGroup', $_SESSION[C('USER_GROUP')]);
		$this->assign('uid', $encryptid);
		header('HTTP/1.1 200 OK');
		$this->display();
	}
	
	//show basic user info page
	public function basicUserInfo() {
		if (!$this->islogin()) {
			$this->redirectedToLoginPage();
		}
		
		$encryptid = $this->_get('uid');
		
		//手机端上传图片时使用
		$imgName = $this->_get('imgName');
		if (!empty($imgName)) {
			$this->assign('imgName', $imgName);
		} 

		if (isset($encryptid) && $encryptid != '') {
			$uid = decryptUid($encryptid);
			$Users = D('Users');
			$userInfo = $Users->getBasicUserInfoByUid($uid);
			$account = $userInfo['account'];
			if (empty($userInfo)) {
				$this->error(L('edit_fialed'));
			}
			if (!$this->hasModifyAuth($userInfo)) {
				$this->error(L('have_no_edit_auth'));
			}
			$birth = substr($userInfo['birthday'],0,11);
			$death = substr($userInfo['deathday'],0,11);
			// $userInfo['birthday'] = $this->sqlDateToPhpDate($birth);
			// $userInfo['deathday'] = $this->sqlDateToPhpDate($death);
			$this->assign('account', $account);
			$this->assign('userInfo', $userInfo);
			$this->assign('encryptid', $encryptid);
			$this->display('editBasicUserInfo');
		} else {
			$this->display('addBasicUserInfo');
		}	
	}
	
	/**
	 * 判断当前登录的用户的是否有修改的权限
	 */
	public function hasModifyAuth($userInfo) {
		$group = $_SESSION[C('USER_GROUP')];
		if ($group == 'agency' && $userInfo['confirmid'] == 1) {
			return false;
		}
		return true;
	}
	
	public function doEditBasicUserInfo() {
		$Users = D('Users');
		if (!$Users->create()) {
			$this->error($Users->getError());
		}
		
		$newpwd = $this->_post('newpwd');
		if (!empty($newpwd)) {
			$data['password'] = md5($newpwd);
		}

		$encryptid = $this->_post('uid');
		$data['uid'] = decryptUid($encryptid);
		$data['account']= $this->_post('account');
		$data['email'] = $this->_post('email');
		$data['firstname'] = $this->_post('firstname');
		$data['secondname'] = $this->_post('secondname');
		$data['lastname'] = $this->_post('lastname');
		$data['gender'] = $this->_post('gender');
		$data['avatar'] = $this->_post('hidAvatar');
		$birth = $this->_post('birthday');
		$death = $this->_post('deathday');
		$data['birthday'] = $this->phpDateToMysqlDate($birth);
		$data['deathday'] = $this->phpDateToMysqlDate($death);
		$data['twodimcode'] = $this->_post('twodimcode');
		$data['relationship'] = $this->_post('relationship');
		$visiable = $this->_post('visiable1');
		$data['pwdhint'] = $this->_post('pwdhint');
		$data['statusid'] = 1;
				
		$data['visiable'] = $visiable;
		if ($visiable == 2) {
			$data['accesspwd'] = '';
			$data['pwdhint'] = '';
		} else {
			$oldAccesspwd = $this->_post('oldAccesspwd');
			$accesspwd = $this->_post('accesspwd');
			if ($oldAccesspwd != $accesspwd) {
				$data['accesspwd'] = md5($accesspwd);
			}
		}
		$condition['account'] = $data['account'];
		$result = $Users->where($condition)->data($data)->save();
		if ($result !== false){
			$encryptid = encryptUid($data['uid']);
			$this->redirect2('/Users/userProfile?uid='.$encryptid);
		} else {
			$this->error(L('save_failed'));
		}
	}
	
	public function doAddBasicUserInfo() {
		if (!$this->isLogin()) {
			$this->redirectedToLoginPage();
		}
		$Users = D('Users');
		if (!$Users->create()) {
			$this->error($Users->getError());
		}
		
		$account = $this->_post('account');
		if($Users->accountExisted($account)){
			$this->error(L('email_existed'));
		}
		$data['account'] = $account;
		
		
		$password = $this->_post('password');
		$accesspwd = $this->_post('accesspwd');
		//当通过邮件，把用户信息发送给用户的时候，需要明文显示密码
		$_SESSION['password_not_md5'] = $password;
		$_SESSION['accesspwd_not_md5'] = $accesspwd;
		$data['password'] = md5($password);
		$data['accesspwd'] = md5($accesspwd);
		
		$data['email'] = $this->_post('email');
		$data['firstname'] = $this->_post('firstname');
		$data['secondname'] = $this->_post('secondname');
		$data['lastname'] = $this->_post('lastname');
		$data['gender'] = $this->_post('gender');
		$data['avatar'] = $this->_post('hidAvatar');
		$birth = $this->_post('birthday');
		$death = $this->_post('deathday');
		$data['birthday'] = $this->phpDateToMysqlDate($birth);
		$data['deathday'] = $this->phpDateToMysqlDate($death);
		$data['twodimcode'] = $Users->createDCodeByAccount($account);
		$data['relationship'] = $this->_post('relationship');
		$visiable = $this->_post('visiable');
		$data['visiable'] = $visiable;
		if ($visiable != 2) {
			if (empty($accesspwd)) {
				$this->error(L('accesspwd_require'));
			}
		} else {
			$data['accesspwd'] = '';
			$data['pwdhint'] = '';
		}
		
		$data['pwdhint'] = $this->_post('pwdhint');
		$data['statusid'] = 1;
		$data['createtime'] = date('Y-m-d H:i:s', time());
		$data['createdby'] = ($_SESSION['GROUP'] == 'common')?2:1;
		//$data['twodimcode'] = '';
		
		if ($uid = $Users->Add($data)){
			if (!empty($uid)) {
				$encryptid = encryptUid($uid);
				$agencyId = $_SESSION[C('AGENCY_AUTH_KEY')];
				$data = array('uid'=>$uid, 'agencyId' => $agencyId);
				$result = $Users->insertUserAgency($data);
				
				//create two dim code
				//$dimcode = $Users->createDCode($encryptid);
				//$Users->save(array('uid'=>$uid,'twodimcode'=>$dimcode));

				if (!empty($result)) {
					$this->redirect2('/Users/userProfile?uid='.$encryptid);
				} else {
					$this->error(L('add_failed'));
				}	
			}
		} else {
			$this->error(L('add_failed'));
		}
	}
	
	public function sqlDateToPhpYear($sqldate) {
		$year = substr($sqldate,0,4);
		return $year;
	}
	
	public function sqlDateToPhpDate($sqldate) {
		/*
		setlocale (LC_TIME, 'fr_FR'); 
		$date = strftime("%d %B %Y",strtotime($sqldate)); 
		return $date;
		*/
		$newDate = date("d/m/Y", strtotime($sqldate));
		return $newDate;
	}
	
	public function sqlDateToFrenchDate($sqldate) {
		$day = substr($sqldate, 0, 2);
		$month = substr($sqldate, 3, 2);
		$year = substr($sqldate,6,4);
		setlocale (LC_TIME, 'fr_FR'); 
		$date = strftime("%d %B %Y",strtotime($year.'-'.$month.'-'.$day)); 
		$date = utf8_encode($date);
		return $date;
	}
	
	public function genealogy() {
		if (!$this->islogin()) {
			$this->redirectedToLoginPage();
		}
		
		$encryptid = $this->_get('uid');
		$uid = decryptUid($encryptid);
		$opt = $_SESSION['opt'];
		if (empty($uid) ) {
			$this->error(L('pls_enter_basic_info'));
		}
		$Users = D('Users');
		if (!$Users->userExisted($uid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		 
		$Genealogy = M('Genealogy');
		$w = array('uid' => $uid);
		$f = array('id', 'uid', 'pid', 'firstname','secondname', 'lastname', 'relationship', 'gender','avatar','birthday','deathday','confirstname','consecondname', 'conlastname','congender','conbirthday','condeathday');
		$r = $Genealogy->where($w)->field($f)->select();
		$data = array();
		foreach ($r as $v) {
			$tmp = array();
			$tmp['id'] = $v['id'];
			$tmp['pid'] = $v['pid'];
			//$tmp['name'] = $v['lastname'].' '.$v['firstname'].' '.$v['secondname'];
			//$tmp['conname'] = $v['conlastname'].' '.$v['confirstname'].' '.$v['consecondname'];
			$tmp['name'] = $v['lastname'].' '.$v['firstname'];
			$tmp['conname'] = $v['conlastname'].' '.$v['confirstname'];
			$tmp['gender'] = $v['gender'];
			$tmp['congender'] = $v['congender'];
			$tmp['birthday'] = $this->sqlDateToPhpYear(substr($v['birthday'], 0, 11));
			$tmp['deathday'] =$this->sqlDateToPhpYear(substr($v['deathday'], 0, 11));
			$tmp['conbirthday'] = $this->sqlDateToPhpYear(substr($v['conbirthday'], 0, 11));
			$tmp['condeathday'] =$this->sqlDateToPhpYear(substr($v['condeathday'], 0, 11));
			if($tmp['birthday'] == '0000') {
				$tmp['birthday'] = '?';
			}
			if($tmp['deathday'] == '0000') {
				$tmp['deathday'] = '?';
			}
			if(empty($tmp['conbirthday'])) {
				$tmp['conbirthday'] = '?';
			}
			if(empty($tmp['condeathday'])) {
				$tmp['condeathday'] = '?';
			}
			$data[$v['id']] = $tmp;
		}
		$this->assign('data', $data);
	
		import("@.ORG.Util.Tree");
		$tree = new Tree();

		$tree->init($data, 0);
		$tree->getTree(0);
		$this->assign('opt', $opt);
		$this->assign('tree', $tree->getStrTree());
		$this->assign('encryptid', $encryptid);
		header('HTTP/1.1 200 OK');
		$this->display();
	}
	
	
	public function getGenealogyInfo() {
		$id = $this->_post('id');
		if (!empty($id)) {
			$this->getGenealogyInfoById($id);
		}
	}
	
	public function getGenealogyInfoById($id) {
		$g = M('Genealogy');
		$w = array('id' => $id);
		$f = array('id','uid', 'pid', 'firstname', 'lastname', 'relationship', 'avatar');
		$result = $g->where($w)->field($f)->find();
		 
		if ($result === false) {
			$this->error(L('operation_failure'));
		} else {
			if ($result != null) {
				header('HTTP/1.1 200 OK');
				echo json_encode(array(status=>1, data=>$result));
			} else {
				header('HTTP/1.1 200 OK');
				echo json_encode(array(status => 0)); //no data
			}
		}
	}
	
	public function addGenealogy() {
		$encryptid = $this->_post('uid');
		$uid = decryptUid($encryptid);
		$pid = $this->_post('pid');
		$opt = $_SESSION['opt'];
		if(!$this->islogin()) {
			$this->redirectedToLoginPage();
		}
		 
		$g = D('Genealogy');
		if (!$g->create()) {
			$this->error($g->getError());
		}
		 
		$data['uid'] = $uid;
		$nodeId = $this->_post('nodeId');
		$data['firstname'] = $this->_post('fname');
		$data['secondname'] = $this->_post('secondname');
		$data['lastname'] = $this->_post('lname');
		$data['relationship'] = $this->_post('relationship');
		$data['avatar'] = $this->_post('avatar');
		$data['gender'] = $this->_post('gender');
		
		$data['birthday'] = $this->phpDateToMysqlDate($this->_post('birthday'));
		$data['deathday'] = $this->phpDateToMysqlDate($this->_post('deathday'));
		
		$data['confirstname'] = $this->_post('confname');
		$data['consecondname'] = $this->_post('consecondname');
		$data['conlastname'] = $this->_post('conlname');
		$data['congender'] = $this->_post('congender');
		
		$conbirthday = $this->_post('conbirthday');
		$condeathday = $this->_post('condeathday');
		if(!empty($conbirthday) && (trim($conbirthday)) != '' ){
			$data['conbirthday'] = $this->phpDateToMysqlDate($conbirthday);
		}
		
		if(!empty($condeathday) && (trim($condeathday)) != ''){
			$data['condeathday'] = $this->phpDateToMysqlDate($condeathday);
		}
		
		
	
		if ($data['relationship'] == 0) { //add parent node
			
			if (!$this->isRoot($nodeId)) {
				$this->error(L('operation_failure_not_root'));
			}
			$data['pid'] = 0;
			$insertId = $g->add($data);
	
			if ($insertId === false) {
				$this->error(L('add_failed'));
			}
	
			//update the node that node's id = $nodeId
			if (!empty($nodeId)) {
				$data1['id'] = $nodeId;
				$data1['pid'] = $insertId;
				if (!$g->save($data1)) {
					$this->error(L('add_failed'));
				} 
			}
			$this->redirect2('/Users/genealogy?uid='.$encryptid);
			
		} else if ($data['relationship'] == 1) { //add child node
			if (!$this->isChild($nodeId)) {
				//$this->error(L('operation_failure_not_child'));
			}
			$data['pid'] = $nodeId;
			if($g->add($data)) {
				$this->redirect2('/Users/genealogy?uid='.$encryptid);
			} else {
				$this->error(L('add_failed'));
			}
		}
	}
	
	public function deleteGenealogyChild() {
		$nodeId = $this->_post('id');
		if (isset($nodeId) && !empty($nodeId)) {
			if ($this->isChild($nodeId)) {
				$g = M('Genealogy');
				if ($g->delete($nodeId)) {
					//$this->success(L('delete_success'), '/Merchant/genealogy');
					$result = array(status => '1');
				} else {
					$result = array(status => '0');
					//$this->error(L('delete_failed'), '/Merchant/genealogy');
				}
			} else {
				$result = array(status => '2');
				//$this->error(L('can_only_delete_child_node'));
			}
			header('HTTP/1.1 200 OK');
			echo json_encode($result);
		}
	}
	
	public function alllife() {
		if(!$this->islogin()) {
			$this->redirectedToLoginPage();
		}
		$encryptid = $this->_get('uid');
		if (empty($encryptid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		$uid = decryptUid($encryptid);
		$opt = $_SESSION['opt'];
		
		$Users = D('Users');
		$data = $Users->getAlllife($uid);
		$data['uid'] = $uid;
		$data['content']= str_replace('\\', '', $data['content']);
		//$data['content'] = str_replace("\\'","'",$data['content'] );
		$this->assign('opt', $opt);
		$this->assign('alllife', $data);
		$this->assign('uid', $encryptid);
		header('HTTP/1.1 200 OK');
		$this->display();
	}
	
	public function doAlllife() {
		$encryptid = $this->_post('uid');
		$uid = decryptUid($encryptid);
		$content = $this->_post('content');
		if (empty($uid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		if (empty($content)) {
			$this->error(L('pls_enter_content'));
		}
		
		$Users = D('Users');
		$alllife = $Users->getAlllife($uid);
		if (empty($alllife)) {
			$data=array('uid' => $uid, 'content' => $content);
			$result = M('UserAlllife')->add($data);
		} else {
			$data=array('id' => $alllife['id'], 'uid' => $uid, 'content'=>$content);
			$result = M('UserAlllife') ->save($data);
		}
		
		if ($result === false) {
			$this->error(L('save_failed'));
		} else {
			$this->redirect2('/Users/alllife?uid='.$encryptid);
		}
	}
	
	public function remark() {
		if (!$this->islogin()) {
			$this->redirectedToLoginPage();
		}
		$encryptid = $this->_get('uid');
		if (empty($encryptid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		$uid = decryptUid($encryptid);
		$opt = $_SESSION['opt'];
		$Users = D('Users');
		$data = $Users->getUserRemark($uid);
		$data['uid'] = $uid;
		$data['remark'] = str_replace('\\', '', $data['remark']);
		$this->assign('opt', $opt);
		$this->assign('remark', $data);
		$this->assign('uid', $encryptid);
		header('HTTP/1.1 200 OK');
		$this->display();
	}
	
	public function doRemark() {
		$encryptid = $this->_post('uid');
		$uid = decryptUid($encryptid);
		$remark = $this->_post('content');
		if (empty($uid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		if (empty($remark)) {
			$this->error(L('pls_enter_content'));
		}
		$Users = D('Users');
		$data = $Users->getBasicUserInfoByUid($uid);
		$data['remark'] = $remark;
		//$result = $Users->save($data);
		$condition['account'] = $data['account'];
		$result = $Users->where($condition)->data($data)->save();
	
		if ($result === false) {
			$this->error(L('save_failed'));
		} else {
			$this->redirect2('/Users/remark?uid='.$encryptid);
		}
	}
	
	public function isRoot($id) {
		$g = M('Genealogy');
		$w = array('id' => $id);
		$f = array('pid');
		$r = $g->where($w)->field($f)->find();
		if ($r['pid'] == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function isChild($id) {
		$g = M('Genealogy');
		$w = array('pid' => $id);
		$f = array('id');
		$f = $g->where($w)->field($f)->select();
		if (count($f) > 0) {
			return false;
		} else {
			return true;
		}
	}
	
	public function catchword() {
		$encryptid = $this->_get('uid');
		if (empty($encryptid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		$uid = decryptUid($encryptid);
		$opt = $_SESSION['opt'];
		
		
		$this->assign('encryptid', $encryptid);
		$this->assign('opt', $opt);
		header('HTTP/1.1 200 OK');
		$this->display();
	}
	
	public function addCatchword() {
		$encryptid = $this->_post('uid');
		$uid = decryptUid($encryptid);
		$content = $this->_post('content');
		
		if (!isset($uid) || empty($uid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		if (!isset($content) || empty($content)) {
			$this->error(L('pls_enter_catchword_content'));
		}
		 
		$word = M('UserCatchword');
		$now = date('Y-m-d H:i:s', time());
		$w = array('uid' => $uid, 'content' => $content, 'createdtime' => $now);
		
		$result = $word->add($w);

		if ($result === false) {
			//$this->error(L('add_failed'));
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 0));
		} else {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 1, data => array(id=>$result,content=>$content)));
		}
	}
	
	public function delCatchword() {
		$id = $this->_post('id');
		if (empty($id)) {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 0, msg => L('delete_failed')));
			exit;
		}
		$result = D('Users')->delCatchwordById($id);
		if ($result === false) {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 0, msg => L('delete_failed')));
			exit;
		} else {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 1));
			exit;
		}
	}
	
	public function updateCatchword() {
		$id = $this->_post('id');
		$content = $this->_post('content');
		if (empty($id)) {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 0, msg => L('operation_failure')));
			exit;
		}
		$result = D('Users')->updateCatchword($id, $content);
		if ($result === false) {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 0, msg => L('operation_failure')));
			exit;
		} else {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 1));
			exit;
		}
	}
	
	public function loadCatchword() {
		$encryptid = $this->_post('uid');
		$uid = decryptUid($encryptid);
		if (!isset($uid) || empty($uid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		$word = M('UserCatchword');
		$w = array('uid' => $uid);
		$f = array('id', 'content');
		$o = array('createdtime' => 'desc');
		$result = $word->where($w)->order($o)->field($f)->select();
		
		if ($result === false) {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 0));
		} else {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 1, data=>$result));
		}
	}
	
	public function userPicture() {
		if (!$this->islogin()) {
			$this->redirectedToLoginPage();
		}
		$encryptid = $this->_get('uid');
		if (empty($encryptid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		$uid = decryptUid($encryptid);
		$opt = $_SESSION['opt'];
		$this->assign('opt', $opt);
		$this->assign('uid', $encryptid);
		header('HTTP/1.1 200 OK');
		$this->display();
	}
	
	public function loadPicture() {
		$encryptid = $this->_post('uid');
		$uid = decryptUid($encryptid);
		if (!isset($uid) || empty($uid)) {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 0));
			exit;
		}
		$p = M('UserPictures');
		$w = array('uid' => $uid);
		$f = array('id', 'name');
		$o = array('createdtime' => 'desc');
		$r = $p->where($w)->order($o)->field($f)->select();
	
		if ($r === false) {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 0));
		} else {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 1, data => $r));
		}
	}
	
	public function insertPicture() {
		$encryptid = $this->_post('uid');
		$uid = decryptUid($encryptid);
		if (!isset($uid) || empty($uid)) {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 0));
			exit;
		}
		 
		$imgNames = $this->_post('imgNames');
		if (!isset($imgNames) || empty($imgNames)) {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 1));
			exit;
		}
		 
		$imgNameArray = split(',', $imgNames);
		$data = array();
		foreach($imgNameArray as $k => $v) {
			if (!empty($v)) {
				$data[$k]['uid'] = $uid;
				$data[$k]['name'] = substr($v, 11, strlen($v)-10);
				//$data[$k]['name'] = $v;
				$data[$k]['createdtime'] = date('Y-m-d H:i:s', time());
			}
		}
	
		$p = M('UserPictures');
		if($p->addAll($data)) {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 2));
		} else {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 3));
		}
	}
	
	public function delPic() {
		$pid = $this->_post('pid');
		if(!empty($pid)) {
			$p = M('UserPictures');
			$w = array('id'=> $pid);
			$result = $p->where($w)->delete();
			if (!empty($result)) {
				header('HTTP/1.1 200 OK');
				echo json_encode(array(status=>1));
			} else {
				header('HTTP/1.1 200 OK');
				echo json_encode(array(status=>1,  msg=>addslashes(L('delete_failed'))));
			}
		} else {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status=>1, msg=>addslashes(L('delete_failed'))));
		}	
	}
	
	public function hasconfirmedUserList() {
		$agencyId = $_SESSION[C('AGENCY_AUTH_KEY')];
		if (isset($agencyId)) {
			//$this->assign('userList', $result);
			$Agency = D('Agency');
			$agencyItem = $Agency->getAgencyBriefInfoById($agencyId);
			if (empty($agencyItem)) {
				$this->error(L('view_failed'));
			}
			$this->assign('agency', $agencyItem);
			$this->display('hasConfirmUserList');			
		} else {
			$this->error(L('pls_login_first'), '/Agency/login');
		}
	}
	
	public function unconfirmedUserList() {
		$agencyId = $_SESSION[C('AGENCY_AUTH_KEY')];
		if (isset($agencyId)) {
			$Agency = D('Agency');
			$agencyItem = $Agency->getAgencyBriefInfoById($agencyId);
			if (empty($agencyItem)) {
				$this->error(L('view_failed'));
			}
			$this->assign('agency', $agencyItem);
			$this->display('unconfirmUserList');
		} else {
			$this->error(L('pls_login_first'), '/Agency/login');
		}
	}
	
	public function loadUserBasicList(){
		$confirmid = $this->_post('confirmid');
		$sKey = $this->_post('sKey');
		load('extend');
		$sKey = h($sKey);
		
		if(!empty($sKey)) {
			$sql = 'select u.`uid`,u.`account`, `firstname`, `lastname`,u.`avatar` , `twodimcode` from pse_users u ';
			$where = "where u.confirmid=1 ";
			str_replace('+', ' ', $sKey);
			$keys = split(' ', trim($sKey));
			foreach($keys as $value) {
				if(!empty($value) && trim($value) != '') {
					$where = $where . " and (firstname like '%$value%' or lastname like '%$value%' or email like '%$value%' ) ";
				}
			}
			
//			if (!empty($sKey)) {
//				$where = $where . " and (firstname like '%$sKey%' or lastname like '%$sKey%' or email like '%$sKey%' ) ";
//			}		
			$where = $where . 'order by u.createtime desc';
			$sql = $sql . $where;
//			echo $sql;
			$result = M()->query($sql);

			if (empty($result)) {
				header('HTTP/1.1 200 OK');
				echo json_encode(array(status=>2, msg=>addslashes(L('no_data'))));
				exit;
			}
			
			foreach($result as $k => $v) {
				$result[$k]['uid'] = encryptUid($v['uid']);
				$result[$k]['account'] = $v['account'];
				$result[$k]['serverhost'] = $_SERVER['HTTP_HOST'];
				$result[$k]['firstname'] = $v['firstname'];
				$result[$k]['lastname'] = $v['lastname'];
				$result[$k]['avatar'] = $v['avatar'];
				$result[$k]['twodimcode'] = $v['twodimcode'];
			}
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 1, data=>$result));
		}else{
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status=>2, msg=>addslashes(L('no_data'))));
			exit;
		}
	}
		
	public function loadUserList() {
		$limit = 10;
		$agencyId = $_SESSION[C('AGENCY_AUTH_KEY')];
		if (isset($agencyId)) {
			$confirmid = $this->_post('confirmid');
			$nowPage = $this->_post('page');
			$sfName = $this->_post('sfName');
			$slName = $this->_post('slName');
			$sEmail = $this->_post('sEmail');
			$sql = 'select u.uid,u.`account`, `firstname`, `lastname`,u.`email`, `createtime` , `twodimcode` from pse_users u, pse_user_agency a ';
			$where = "where u.uid=a.uid and u.confirmid=$confirmid and agencyId=$agencyId ";
			if (!empty($sfName)) {
				$where = $where . " and firstname like '%$sfName%' ";
			}
			if (!empty($slName)) {
				$where = $where . " and lastname like '%$slName%' ";
			}
			if (!empty($sEmail)) {
				$where = $where . " and email like '%$sEmail%' ";
			}
			
			//echo $sql;
			
			$countSql = 'select count(*) c from pse_users u, pse_user_agency a ' . $where;
			$count = M()->query($countSql);
			$count = $count[0]['c'];	
			import('ORG.Util.Page');
			$Page = new Page($count, $limit);
			$totalPages = $Page->getTotalPages();
			$firstRow = $Page->firstRow;
			$listRows = $Page->listRows;
			$pageInfo = array(totalPages => $totalPages, nowPage => $nowPage);
			
		    $start = ($nowPage - 1) * $limit;
			
			$where = $where . 'order by u.createtime desc ';
			$where = $where . "LIMIT $start, $limit";
			$sql = $sql . $where;
			$result = M()->query($sql);
			if (empty($result)) {
				header('HTTP/1.1 200 OK');
				echo json_encode(array(status=>2, msg=>addslashes(L('no_data'))));
				//echo json_encode(array(status=>2, msg=>addslashes($start)));
				exit;
			}
			
			foreach($result as $k => $v) {
				$result[$k]['uid'] = encryptUid($v['uid']);
				$result[$k]['account'] = $v['account'];
				$result[$k]['createtime'] = substr($v['createtime'], 0, 11);
				$result[$k]['downloadText'] = L('download');
			}
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 1, data=>$result, page => $pageInfo));
		} else {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status=>0, msg=>addslashes(L('load_data_failed'))));
		}
	}
	
	public function finish() {
		$uid = $this->_get('uid');
		if (empty($uid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		
		$Users = D('Users');
		if($Users->userExisted($uid)) {
			$account = $Users->getUserAccount($uid);
			if (!empty($account)) {
				$data = array('uid'=> $uid, 'account' => $account, 'confirmid' => 1);
				$result = $Users->save($data);
				$this->success(L('add_success'), '/Agency/index');
			} else {
				$this->error(L('add_failed'), '/Users/userProfile?uid='.$uid);
			}
		} else {
			$this->error(L('pls_enter_basic_info'));
		}
	}
	
	
	public function islogin() {
		if (isset($_SESSION[C('USER_AUTH_KEY')]) || isset($_SESSION[C('AGENCY_AUTH_KEY')])) {
			return true;
		} else {
			return false;
		}
	}
	
	public function redirectedToLoginPage() {
		$userGroup = $_SESSION[C('USER_GROUP')];
		$redirectUrl = ($userGroup == 'agency')?'/Agency/login':'/Users/login';
		$this->error(L('pls_login_first'), $redirectUrl);
	}
	
	public function createTwoDimCode() {
		$encryptid = $this->_post('uid');
		
		
		if(!empty($encryptid)) {
			$Users = D('Users');
			$pngName = $Users->createDCode($encryptid);			
			if (!empty($pngName)) {
				header('HTTP/1.1 200 OK');
				echo json_encode(array(status => 1, pngName => $pngName));
			} else {
				header('HTTP/1.1 200 OK');
				echo json_encode(array(status => 0, msg=>addslashes(L('create_failed'))));
			}
		} else {
			header('HTTP/1.1 200 OK');
			echo json_encode(array(status => 0, msg=>addslashes(L('create_failed'))));
		} 
	}
	
	public function registUserInfo() {
		header('HTTP/1.1 200 OK');
		$this->display();
	}
	
	public function phpDateToMysqlDate($phpdate) {
		$day = substr($phpdate,0,2);
		$month = substr($phpdate,3,2);
		$year = substr($phpdate,6,4);
		return $year . '-' . $month . '-' . $day;
	}
	
	public function doRegistUserInfo() {
		$data['account'] = $this->_post('account');
		$data['email'] = $this->_post('email');
		$data['password'] = md5($this->_post('password'));
		$data['firstname'] = $this->_post('firstname');
		$data['secondname'] = $this->_post('secondname');
		$data['lastname'] = $this->_post('lastname');
		$data['gender'] = $this->_post('gender');
		$data['avatar'] = $this->_post('hidAvatar');
		$data['birthday'] = $this->phpDateToMysqlDate($this->_post('birthday'));
		$data['deathday'] = $this->phpDateToMysqlDate($this->_post('deathday'));	
		$data['relationship'] = $this->_post('relationship');
		$data['visiable'] = $this->_post('visiable');
		$accesspwd = $this->_post('accesspwd');
		$data['accesspwd'] = md5($accesspwd);
		$data['pwdhint'] = $this->_post('pwdhint');
		$data['createtime'] = date('Y-m-d H:i:s', time());
		$data['createdby'] = 2;
		$data['statusid'] = 1;
		$data['paid'] = 0;
		
		$Users = D('Users');
		//Create QR code
		$data['twodimcode'] = $Users->createDCodeByAccount($account);		

		if(empty($data['account']) || empty($data['password']) || empty($data['email'])){
			$this->error(L('empty_require'));
		}
		
		if($Users->accountExisted($data['account'])) {
			$this->error(L('email_existed'));
		}
		
		if ($uid = $Users->add($data)) {
			$_SESSION[C('USER_GROUP')] = 'visiter';
			$encryptid = encryptUid($uid);
			$this->success(L('userinfo_has_saved'),'/Users/preview?uid='.$encryptid);
		} else {
			$this->error(L('add_failed'));
		}
	}
	
	public function payfor() {
		$uid = $this->_get('uid');
		$Users = D('Users');
		$w = array('uid'=>$uid, 'statusid'=>1);
		$f = array('uid');
		$result = $Users->where($w)->field($f)->find();
		
		if (empty($result)) {
			$this->error(L('pls_regist_first'));
		} else {
			$this->assign('uid', $uid);
			header('HTTP/1.1 200 OK');
			$this->display();
		}
	}
	
	/**
	 * 预览用户信息
	 */
	public function preview() {
		$encryptid = $this->_get('uid');
		if (empty($encryptid)) {
			$this->error(L('pls_enter_basic_info'));
		}
		$uid = decryptUid($encryptid);
		
		$userInfo = D('Users')->getBasicUserInfoByUid($uid);
		$userInfo['birthday'] = $this->sqlDateToFrenchDate($userInfo['birthday']);
		$userInfo['deathday'] = $this->sqlDateToFrenchDate($userInfo['deathday']);
		if ($userInfo['visiable'] == 2) {
			$detect = new Mobile_Detect();
			if ($detect->isMobile()) {
				$this->_view($userInfo, 'life');
			} else {
				$this->_view($userInfo, 'home');
			}
		} else if ($userInfo['visiable'] == 1) {
			$this->assign('uid', $encryptid);
			$this->assign('userInfo', $userInfo);
			header('HTTP/1.1 200 OK');
			$this->display('lessUserView');
		} else if ($userInfo['visiable'] == 0) {
			$this->assign('uid', $encryptid);
			$this->assign('userInfo', $userInfo);
			header('HTTP/1.1 200 OK');
			$this->display('enterAccessPwd');
		}
	}
	
	public function userView() {
		$Users = D('Users');
		$account = $this->_get('account');
		$userInfo = $Users->getBasicUserInfoByAccount($account);
		$uid = $userInfo['uid'];
		$encryptid = encryptUid($uid);
//		$encryptid = $this->_get('uid');
//		$uid = decryptUid($encryptid);
		
//		$userInfo = $Users->getBasicUserInfoByUid($uid);

		if (empty($userInfo)) {
			echo $uid;
			$this->error('view_failed');
		}
		
		// check whether have been paid, if it created by user
		if ($userInfo['createdby'] == 2 && $userInfo['paid'] == 0) {
			$this->error(L('pls_checkout'),"/Users/login");
		} 
		
		if ($userInfo['createdby'] == 1 && $userInfo['confirmid'] == 0) {
			$this->error(L('view_failed_hasnot_confirmed'));
		}
		
		$_SESSION[C('USER_GROUP')] = 'visiter';
		//unset($_SESSION[C('USER_AUTH_KEY')]);
		//unset($_SESSION[C('AGENCY_AUTH_KEY')]);
		$birth = $userInfo['birthday'];
		$death = $userInfo['deathday'];
		$userInfo['birthday'] = $this->sqlDateToFrenchDate($birth);
		$userInfo['deathday'] = $this->sqlDateToFrenchDate($death);
		if ($userInfo['visiable'] == 2) {
			$detect = new Mobile_Detect();
			if ($detect->isMobile()) {
				$this->_view($userInfo, 'life');
			} else {
				$this->_view($userInfo, 'home');
			}
		} else if ($userInfo['visiable'] == 1) {
			$this->assign('uid', $encryptid);
			$this->assign('userInfo', $userInfo);
			header('HTTP/1.1 200 OK');
			$this->display('lessUserView');
		} else if ($userInfo['visiable'] == 0) {
			$this->assign('uid', $encryptid);
			$this->assign('userInfo', $userInfo);
			header('HTTP/1.1 200 OK');
			$this->display('enterAccessPwd');
		}
	}
	
	public function view() {
		$encryptid = $this->_get('uid');
		$uid = decryptUid($encryptid);
		$type = $this->_get('type');
		if (!empty($uid) && !empty($type)) {
			$userInfo = D('Users')->getBasicUserInfoByUid($uid);
			$birth = $userInfo['birthday'];
			$death = $userInfo['deathday'];
			$userInfo['birthday'] = $this->sqlDateToFrenchDate($birth);
			$userInfo['deathday'] = $this->sqlDateToFrenchDate($death);
			$this->_view($userInfo, $type);
		}
	}
	
	private function _view($userInfo, $type) {
		if (!empty($userInfo)) {
			
			$uid = $userInfo['uid'];
			$encryptid = encryptUid($uid);
			$this->assign('group', $_SESSION[C('USER_GROUP')]);
			$this->assign('uid', $encryptid);
			$this->assign('account',$_SESSION['account']);
			$this->assign('opt', $_SESSION['opt']);
			$this->assign('confirmed', $userInfo['confirmid']);
			$this->assign('paid', $userInfo['paid']);
			$this->assign('createdby', $userInfo['createdby']);
			$birth = $userInfo['birthday'];
			$death = $userInfo['deathday'];
			//$userInfo['birthday'] = $this->sqlDateToPhpDate($birth);
			//$userInfo['deathday'] = $this->sqlDateToPhpDate($death);
			$this->assign('userInfo', $userInfo);
			
			$Users = D('Users');
			if ($type == 'home') {
				$by = ($birth == '0000-00-00') ? 0: date('Y', strtotime($birth));
				$dy = ($death == '0000-00-00') ? 0: date('Y', strtotime($death));
				if(!empty($by) && !empty($dy)) {
					$age = $dy - $by + 1;
					$userInfo['age'] = $age;
				} else {
					$userInfo['age'] = '';
				}
				
		
				$pics = $Users->getPicturesByUid($uid);
				$alllife = $Users->getAlllife($uid);
				//过滤html标签
				//$alllife['content'] = clear_html_tag($alllife['content']);
				$catchwords = $Users->getCatchwordsByUid($uid);
				$gen = $Users->getGenealogyUserName($uid);
					
				$this->assign('genealogyName', $gen);
				if (count($catchwords)>=3) {
					$catchwords = array_slice($catchwords, 0, 2);  
				}
				if (count($pics)>=5) {
					$pics = array_slice($pics, 0, 5);  
				}
				foreach ($catchwords as &$word) {
					$word['content'] =  str_replace('\\','',$word['content']);
					$word['content'] =  str_replace('<br>','',$word['content']);
					$word['content'] =  str_replace('<br />','',$word['content']); 
				}
				$this->assign('catchwords', $catchwords);
				$this->assign('alllife', $alllife);
				$this->assign('userPics', $pics);
				$remark = $userInfo['remark'];
				$remark = str_replace('\\','',$remark);
				$this->assign('remark',$remark);
				$this->assign('currNav', 'navHome');
				header('HTTP/1.1 200 OK');
				$this->display('userHomeView');
			} else if ($type == 'life'){
				$data = $Users->getAlllife($uid);
				$this->assign('currNav', 'navAlllife');
				$this->assign('alllife', $data);
				header('HTTP/1.1 200 OK');
				$this->display('alllifeView');
				
			} else if ($type == 'pic') {
				$pics = $Users->getPicturesByUid($uid);
				//当从手机端浏览时，为每张图片生成缩微图
				$detect = new Mobile_Detect();
				//if($detect->isMobile()) {
					foreach ($pics as $k => $v) {
						$thumbName = thumbImg('.'.$v['name'],'jpg',275,187);
						$pics[$k]['name'] = substr($thumbName, 1);
					}
				//}
				$this->assign('currNav', 'navPic');
				$this->assign('pics', $pics);
				header('HTTP/1.1 200 OK');
				$this->display('userPictureView');
			} else if ($type == 'word') {
				$catchwords = $Users->getCatchwordsByUid($uid);
				$this->assign('currNav', 'navWord');
				$this->assign('catchwords', $catchwords);
				header('HTTP/1.1 200 OK');
				$this->display('catchwordView');
			} else if ($type == 'remark') {
				$remark = $Users->getUserRemark($uid);
				$this->assign('currNav', 'navRemark');
				$this->assign('remark', $remark);
				header('HTTP/1.1 200 OK');
				$this->display('remarkView');
			} else if ($type == 'gen') {
			/*
				$Gen = D('Genealogy');
				$genData = $Gen->getGenData($uid);
				$genTree = $Gen->getGenTree($genData);
			*/
				$Genealogy = M('Genealogy');
				$w = array('uid' => $uid);
				$f = array('id', 'uid', 'pid', 'firstname','secondname', 'lastname', 'relationship', 'gender','avatar','birthday','deathday','confirstname','consecondname', 'conlastname','congender','conbirthday','condeathday');
				$r = $Genealogy->where($w)->field($f)->select();
				$data = array();
				foreach ($r as $v) {
					$tmp = array();
					$tmp['id'] = $v['id'];
					$tmp['pid'] = $v['pid'];
			//$tmp['name'] = $v['lastname'].' '.$v['firstname'].' '.$v['secondname'];
			//$tmp['conname'] = $v['conlastname'].' '.$v['confirstname'].' '.$v['consecondname'];
					$tmp['name'] = $v['lastname'].' '.$v['firstname'];
					$tmp['conname'] = $v['conlastname'].' '.$v['confirstname'];
					$tmp['gender'] = $v['gender'];
					$tmp['congender'] = $v['congender'];
					$tmp['birthday'] = $this->sqlDateToPhpYear(substr($v['birthday'], 0, 11));
					$tmp['deathday'] =$this->sqlDateToPhpYear(substr($v['deathday'], 0, 11));
					$tmp['conbirthday'] = $this->sqlDateToPhpYear(substr($v['conbirthday'], 0, 11));
					$tmp['condeathday'] =$this->sqlDateToPhpYear(substr($v['condeathday'], 0, 11));
					if($tmp['birthday'] == '0000') {
						$tmp['birthday'] = '?';
					}
					if($tmp['deathday'] == '0000') {
						$tmp['deathday'] = '?';
					}
					if(empty($tmp['conbirthday'])) {
						$tmp['conbirthday'] = '?';
					}
					if(empty($tmp['condeathday'])) {
						$tmp['condeathday'] = '?';
					}
					$data[$v['id']] = $tmp;
				}
				$this->assign('data', $data);
	
				import("@.ORG.Util.Tree");
				$tree = new Tree();

				$tree->init($data, 0);
				$tree->getTree(0);
				//TODO
				$this->assign('currNav', 'navGenealogy');
				$this->assign('tree', $tree->getStrTree());
				header('HTTP/1.1 200 OK');
				$this->display('genealogyView');
			}
		} else {
			$this->error(L('view_failed'));
		}
	}
	
	public function clearVisiterIdSession(){
		if(isset($_SESSION['VISITER_ID'])) {
			unset($_SESSION['VISITER_ID']);
		}
	}
	
	public function checkAccessPwd() {
		$accesspwd = $this->_post('accesspwd');
		$encryptid = $this->_post('uid');
		$uid = decryptUid($encryptid);
		
		$Users = D('Users');
		$w = array('uid' => $uid);
		$f = array('uid','accesspwd');
		$result = $Users->where($w)->field($f)->find();

		if(md5($accesspwd) == $result['accesspwd']) {
			$detect = new Mobile_Detect();
			if (!$detect->isMobile()) {
				$this->redirect2("/Users/view?uid=$encryptid&type=home");
			} else {
				$this->redirect2("/Users/view?uid=$encryptid&type=life");
			}
		} else {
			$this->error(L('wrong_password'));
		}
	}
	
	public function downloadImg() {
		$filename = $this->_get('fn');
		$data = file_get_contents('.'.$filename); // 读文件内容
		$name = 'QRCode.jpg';
		$this->force_download($name, $data);
	}

	public function force_download($filename = '', $data = '')
	{
		if ($filename == '' OR $data == '')
		{
			return FALSE;
		}
	
		// Try to determine if the filename includes a file extension.
		// We need it in order to set the MIME type
		if (FALSE === strpos($filename, '.'))
		{
			return FALSE;
		}
	
		// Grab the file extension
		$x = explode('.', $filename);
		$extension = end($x);
	
		$mimes = array(     'bmp' => array('image/bmp','application/octet-stream'),
				'gif' => array('image/gif','application/octet-stream'),
				'jpeg'  =>  array('image/jpeg', 'image/pjpeg', 'application/octet-stream'),
				'jpg'   =>  array('image/jpeg', 'image/pjpeg', 'application/octet-stream'),
				'jpe'   =>  array('image/jpeg', 'image/pjpeg', 'application/octet-stream'),
				'png'   =>  array('image/png',  'image/x-png', 'application/octet-stream')
		);
	
		// Set a default mime if we can't find it
		if ( ! isset($mimes[$extension]))
		{
			$mime = 'application/octet-stream';
		}
		else
		{
			$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}
	
		// Generate the server headers
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			header("HTTP/1.0 200 OK");
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($data));
		}
		else
		{
			header("HTTP/1.0 200 OK");
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($data));
		}
	
		exit($data);
	}
	
	public function confirmSave() {
		$encryptid = $this->_get('uid');
		$uid = decryptUid($encryptid);
		if (empty($encryptid)) {
			$this->error(L('pls_enter_basic_info'));
		} else {
			$Users = D('Users');
			$userInfo = $Users->getBasicUserInfoByUid($uid);
			if (!empty($userInfo)) {
				$data['uid'] = $userInfo['uid'];
				$data['account'] = $userInfo['account'];
				$data['confirmid'] = 1;
				$condition['account'] = $data['account'];
				$result = $Users->where($condition)->data($data)->save();
				
				if ($result == false) {
					$this->error(L('save_failed'));
				} 
				//send email
				$result = $Users->sendEmail($userInfo);
				
				if ($result == false) {
					$this->error(L('email_send_failed'));
				} 
				$opt = $_SESSION['opt'];
				if ($opt == 'agency_opt') {
					$this->success(L('user_info_has_send_to_email'),'/Agency/index' );
				} else if ($opt == 'user_opt') {
					$this->success(L('user_info_has_send_to_email'), '/Users/index');
				}	
			}
		}
	}
	
	public function tempStorage() {
		$uid = $this->_get('uid');
		$opt = $_SESSION['opt'];
		if (empty($uid)) {
			$this->error(L('pls_enter_basic_info'));
		} else {
			if ($opt == 'agency_opt') {
				$this->redirect('/Agency/index');
			} else if ($opt == 'user_opt') {
				$this->redirect('/Users/index');
			}
		}
	}
	
	public function uploadAvatar() {
		$encryptid = $this->_get('uid');
		$imgName = $this->_get('imgName');
	
			$this->assign('uid', $encryptid);
			$this->assign('imgName', $imgName);
			header('HTTP/1.1 200 OK');
			$this->display();
		
	}
}