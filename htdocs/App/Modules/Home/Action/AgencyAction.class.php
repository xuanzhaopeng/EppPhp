<?php
class AgencyAction extends Action{
    public function index(){
    	if (!isset($_SESSION[C('AGENCY_AUTH_KEY')])) {
    		$this->error(L('pls_login_first'), '/Agency/login');
    	}
    	
    	$Agency = D('Agency');
    	$id = $_SESSION[C('AGENCY_AUTH_KEY')];
    	$agency = $Agency->getAgencyBriefInfoById($id);
    	if (empty($agency)) {
    		$this->error($_SESSION[C('AGENCY_AUTH_KEY')]);
    	}
    	
    	$this->assign('agency', $agency);
    	$agencyid = encryptUid($agency['id']);
    	$this->assign('agencyid', $agencyid);
		header('HTTP/1.1 200 OK');
        $this->display();
    }
    
    public function login() {
		if(isset($_SESSION[C('AGENCY_AUTH_KEY')])){
			$this->redirect('/Agency/index');
		}else {
			header('HTTP/1.1 200 OK');
			$this->display();
		}
    }
    
    public function createAgency() {
		header('HTTP/1.1 200 OK');
    	$this->display();
    }
    
    public function editAgency() {
    	$encryptid = $this->_get('id');
    	
    	if (empty($encryptid)) {
    		$this->error(L('save_failed'));
    	}
    	$agencyId = decryptUid($encryptid);
    	$agencyInfo = D('Agency')->getAgencyBriefInfoById($agencyId);
    	if (empty($agencyInfo)) {
    		$this->error(L('edit_failed'));
    	}
    	$this->assign('optType', 'edit');
    	$this->assign('agency', $agencyInfo);
    	$this->assign('agencyid', $encryptid);
    	$this->display('editAgency');
    }
    
    public function checkAgency() {
    	$Agency = M('Agency');
    	$email = $this->_post('email');
    	$email = trim($email);
    	$w = array('email'=> $email, 'confirmid' => 1);
    	$f = array('id', 'email', 'password','name','logo','confirmid');
    	$agInfo = $Agency->where($w)->field($f)->find();
    	 
    	if ($agInfo['password'] != md5($_POST['password'])){
    		$this->error(L('wrong_password'));
    	}
    	 
    	$data['id'] = $agInfo['id'];
    	$ip = get_client_ip();
    	$data['email'] = $agInfo['email'];
    	$data['last_login_time'] = date('Y-m-d H:i:s', time());
    	$data['login_count'] = array('exp', 'login_count+1');
    	$data['login_ip'] = $ip;
    	$_SESSION['agencyname'] = $agInfo['name'];
    	if(!$Agency->save($data)) {
    		$this->error(L('login_failed'));
    	}

    	$_SESSION[C('AGENCY_AUTH_KEY')] = $agInfo['id'];
		unset($_SESSION[C('USER_AUTH_KEY')]); 
    	$_SESSION[C('USER_GROUP')] = 'agency';
    	$_SESSION['opt'] = 'agency_opt';

    	$remember = $this->_post('remember');
    	$password = $this->_post('password');
    	if ($remember == 1) {
    		setcookie('agency_email',$email,time()+3600);
    		setcookie('agency_pwd',$password,time()+3600);
    		setcookie('agency_remember',$remember,time()+3600);
    	} else {
    		setcookie('agency_email',$name,time()-3600);
    		setcookie('agency_pwd',$password,time()-3600);
    		setcookie('agency_remember',$remember,time()-3600);
    	}

    	$this->redirect('/Agency/index');
    }
    
    
    public function saveInfo() {
    	$email = $this->_post('email');

    	if(empty($email))  {
    		$this->error(L('email_require'));
    	}
    	
    	$Agency = D('Agency');
    	
    	$optType = $this->_post('optType');
		if ($optType=='add' && $Agency->emailExisted($email)) {
			$this->error(L('email_existed'));
		}    	
		
		if ($optType== 'edit') {
			$encryptid = $this->_post('agencyId');
			$agencyId = decryptUid($encryptid);
			$res = $Agency->getAgencyEmailById($agencyId);
			//if ($res['email'] != $email && $Agency->emailExisted($email)){
			//	$this->error(L('email_existed'));
			//}
		}
		
		$data['email'] = $email;
		$data['siret'] = $this->_post('siret');
		$data['network'] = $this->_post('network');
    	$data['name'] = $this->_post('name');
    	$data['road_name'] = $this->_post('roadname');
    	$data['city'] = $this->_post('city');
    	$data['postcode'] = $this->_post('postcode');
    	$data['phone'] = $this->_post('phone');
    	$data['firstname'] = $this->_post('firstname');
    	$data['lastname'] = $this->_post('lastname');
    	$data['contactPhone'] = $this->_post('contactPhone');
    	$data['contactEmail'] = $this->_post('contactEmail');
    	$data['logo'] = $this->_post('hidAvatar');
    	
    	if ($optType == 'add' ) {
    		$data['createdtime'] = date('Y-m-d h:i:s', time());
    		$data['statusid'] = 1;
    		$result = $Agency->add($data);
    		if (empty($result)) {
    			$this->error(L('save_failed'));
    		}
			$this->success("Felicitation!",'/Agency/login');
    		//$this->redirect('createAgencyFinished');
    	} else {
    		$agencyId = $this->_post('agencyId');
    		$agencyId = decryptUid($agencyId);
    		if (empty($agencyId)) {
    			$this->error(L('save_failed'));
    		}
    		$data['id'] = $agencyId;
			$result = $Agency->save($data);    		
    		if ($result === false) {
    			$this->error(L('save_failed'));
    		}
    		$agencyId = encryptUid($agencyId);
    		$this->redirect('/Agency/index');
    	}    	
    }
    
    public function uploadAvatar() {
    	if (!empty($_FILES)) {
    		$thumbPrefix = $_POST['prefix'];
    		$thumbW = $_POST['thumbW'];
    		$thumbH = $_POST['thumbH'];
    		$res = uploadImg($thumbPrefix, $thumbW, $thumbH);
    		if ($res !== false) {
				header('HTTP/1.1 200 OK');
    			echo json_encode(array(status => 1, imgName =>$res));
    		} else {
				header('HTTP/1.1 200 OK');
    			echo json_encode(array(status=>0));
    		}
    	}
    }
    
    public function userProfile() {
		if ($this->islogin()) {
			$this->display();
		} else {
			$this->error(L('pls_login_first'));
		}
    }
    
    
	public function islogin() {
		if (isset($_SESSION[C('AGENCY_AUTH_KEY')])) {
			return true;
		} else {
			return false;
		}
	}
}
?>