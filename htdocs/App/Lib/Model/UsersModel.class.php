<?php
header("content-Type: text/html; charset=utf-8"); 

// merchant model
class UsersModel extends Model {
//    public $_validate	=	array(
//    	array('email','require','{%email_require}'),
//    	array('password','require','{%password_require}'),
//        );
    
	public $_validate = array(
		array('account','require','{account_require}'),
		array('password','require','{%password_require}')
	);
	
	public function accountExisted($account) {
		$w = array('account' => $account);
    	$f = array('uid');
    	$result = $this->where($w)->field($field)->find();

    	if (empty($result)) {
    		return false;
    	} else {
    		return true;
    	}
	}
	
//    public function emailExisted($email) {
//    	$w = array('email' => $email);
//    	$f = array('uid');
//    	$result = $this->where($w)->field($field)->find();
//
//    	if (empty($result)) {
//    		return false;
//    	} else {
//    		return true;
//    	}
//    }
    
    public function userExisted($uid) {
    	$w = array('uid' => $uid);
    	$f = array('uid');
    	$result = $this->where($w)->field($field)->find();
    	if (empty($result)) {
    		return false;
    	} else {
    		return true;
    	}    	
    }
    
    
    public function getUserInfoById($uid) {
    	return $this->where(array('uid'=>$uid))->find();
    }
    
	public function getUserInfoByAccount($account) {
		$userInfo =  $this->where(array('account'=>$account))->find();
		$birthday = substr($userInfo['birthday'], 0, 11);
    	$deathday = substr($userInfo['deathday'], 0, 11);
    	$by = substr($birthday, 0, 4);
    	$dy = substr($deathday, 0, 4);
    	$userInfo['age'] = $dy - $by;
		return $userInfo;
    	
    }
    
    public function getBasicUserInfoByUid($uid) {
    	$w = array('uid' => $uid);
    	$w['statusid'] = 1;
    	return $this->getBasicUserInfo($w);
    }
    
	public function getBasicUserInfoByAccount($account) {
    	$w = array('account' => $account);
    	$w['statusid'] = 1;
    	return $this->getBasicUserInfo($w);
    }

//    public function getBasicUserInfoByEmail($email) {
//    	$w = array('email' => $email);
//    	$w['statusid'] = 1;
//    	return $this->getBasicUserInfo($w);
//    }
    
	public function sqlDateToPhpDate($sqldate) {
		/*
		setlocale (LC_TIME, 'fr_FR'); 
		$date = strftime("%d %B %Y",strtotime($sqldate)); 
		return $date;
		*/
		$newDate = date("d/m/Y", strtotime($sqldate));
		return $newDate;
	}
	
	public function sqlDateToPhpYear($sqldate) {
		$year = substr($sqldate,0,4);
		return $year;
	}
	
    public function getBasicUserInfo($where) {
    	$f = array('uid','account', 'email', 'password','firstname', 'secondname', 'lastname','gender', 'avatar', 'birthday', 'deathday','twodimcode','relationship', 'visiable', 'accesspwd', 'pwdhint', 'remark', 'confirmid', 'paid','createdby');
    	$userInfo = $this->where($where)->field($f)->find();
    	if ($userInfo != null) {
    		$birthday = substr($userInfo['birthday'], 0, 11);
    		$deathday = substr($userInfo['deathday'], 0, 11);
    		$by = substr($birthday, 0, 4);
    		$dy = substr($deathday, 0, 4);
			$userInfo['birthday'] = $this->sqlDateToPhpDate($birthday);
			$userInfo['deathday'] = $this->sqlDateToPhpDate($deathday);
    		$userInfo['age'] = $dy - $by;
    	}
    	
		return $userInfo;
    }
    
    public function getGenealogyUserName($uid) {
    	$Genealogy = M('Genealogy');
    	$w = array('uid' => $uid);
    	$f = array('firstname', 'lastname','confirstname','conlastname');
    	$result = $Genealogy->where($w)->field($f)->select();
    	if($result === false) {
    		return false;
    	}
    	 
    	if ($result != null) {
    		$name = '';
    		foreach($result as $v) {
    			//print_r($v);
				if(empty($v['confirstname']) || trim($v['confirstname']) == '') {
					$name = $name .'<li>'.$v['firstname'].' '.$v['lastname'].'</li>';
				}else{
					$name = $name .'<li>'.$v['firstname'].' '.$v['lastname'].' (Conjoint: '.$v['confirstname'].' '.$v['conlastname'].')</li>';
				}
    		}
    		return $name;
    	} else {
    		return null;
    	}
    }

//    public function getGenealogyTree($uid) {
//    	
//    }

    public function getUserAccount($uid) {
    	$w = array('uid' => $uid);
    	$f = array('account');
    	$result = $this->where($w)->field($f)->find();
    	return $result['account'];
    }
    
    public function getUserEmail($uid) {
    	$w = array('uid' => $uid);
    	$f = array('email');
    	$result = $this->where($w)->field($f)->find();
    	return $result['email'];
    }
    
    
    public function getUserRemark($uid) {
    	$w = array('uid' => $uid);
    	$f = array('uid', 'remark');
    	$remark=  $this->where($w)->field($f)->find();
		$remark = str_replace("\'","'",$remark);
		return $remark;
    }
    
    public function getPicturesByUid($uid) {
    	$Pictures = D('UserPictures');
    	$w = array('uid' => $uid);
    	$f = array('id', 'name');
    	$o = array('createdtime' => 'desc');
    	$pics = $Pictures->where($w)->order($o)->field($f)->select();
    	return $pics;
    }
    
    public function getCatchwordsByUid($uid) {
    	$Catchword = D('UserCatchword');
    	$w = array('uid' => $uid);
    	$f = array('id', 'content');
    	$o = array('createdtime' => 'desc');
    	$words = $Catchword->where($w)->order($o)->field($f)->select();
    	return $words;
    }
    
    public function getCount() {
    	return $this->count();
    }
    
    public function delCatchwordById($id) {
    	$Catchword = D('UserCatchword');
    	$w = array('id' => $id);
    	return $Catchword->where($w)->delete();
    }
    
    public function updateCatchword($id, $content) {
    	$Catchword = D('UserCatchword');
    	$data = array('id' => $id, 'content' => $content);
    	return $Catchword->save($data);
    }
    
    public function getAlllife($uid) {
    	$allLife = D('UserAlllife');
    	$w = array('uid' => $uid);
    	$f = array('id', 'content');
    	$o = array('createdtime' => 'desc');
    	$result = $allLife->where($w)->order($o)->field($f)->find();
		$result = str_replace("\'","'",$result);
    	return $result;
    }
    
    public function insertUserAgency($data) {
    	$userAgency = M('UserAgency');
    	return $userAgency->add($data);
    }
    
    public function hasConfirmed($uid) {
    	$w = array('uid' => $uid);
    	$f = array('confirmid');
     	$result = $this->where($w)->field($f)->find();
     	if($result['confirmid'] == 1) {
     		return true;
     	} else {
     		return false;
     	}
    }
    
    public function createDCode($uid) {
    	$decryptid = decryptUid($uid);
    	$account = $this->getUserAccount($decryptid);
    	
//    	$viewUrl = 'http://'.$_SERVER['HTTP_HOST'].'/Users/userView?uid='.$uid;
		$viewUrl = 'http://'.$_SERVER['HTTP_HOST'].'/'.$account;
    	try {
    		$pngAbsoluteFilePath = './Uploads/';
    		$filename = date("YmdHis") . '_' . rand(10000, 99999) . '.png';
    		$pngAbsoluteFilePath = $pngAbsoluteFilePath .$filename;
    		 
    		vendor('phpqrcode.phpqrcode');
    		$png = QRcode::png($viewUrl, $pngAbsoluteFilePath, 'QR_ECLEVEL_H');
    		return substr($pngAbsoluteFilePath, 1, strlen($pngAbsoluteFilePath));
    	} catch(Exception $ex){
    		return false;
    	}
    }
	
	public function createDCodeByAccount($account) {
    	
//    	$viewUrl = 'http://'.$_SERVER['HTTP_HOST'].'/Users/userView?uid='.$uid;
		$viewUrl = 'http://'.$_SERVER['HTTP_HOST'].'/'.$account;
    	try {
    		$pngAbsoluteFilePath = './Uploads/';
    		$filename = date("YmdHis") . '_' . rand(10000, 99999) . '.png';
    		$pngAbsoluteFilePath = $pngAbsoluteFilePath .$filename;
    		 
    		vendor('phpqrcode.phpqrcode');
    		$png = QRcode::png($viewUrl, $pngAbsoluteFilePath, 'QR_ECLEVEL_H');
    		return substr($pngAbsoluteFilePath, 1, strlen($pngAbsoluteFilePath));
    	} catch(Exception $ex){
    		return false;
    	}
    }
    
    public function checkoutComplete($uid) {
//    	$email = $this->getUserEmail($uid);
//		$data = array('uid' => $uid, 'email' => $email,'paid' => 1);
		$account = $this->getUserAccount($uid);
		$data['paid'] = 1;
		$data['confirmid'] = 1;
		$condition['uid'] = $uid;
		$result = $this->where($condition)->data($data)->save();
		return $result;
    }
    
    function sendEmail($userInfo) {
    	if (empty($userInfo)) {
    		return false;
    	}
    
    	$uid = encryptUid($userInfo['uid']);
//    	$url = 'http://'.$_SERVER['HTTP_HOST'].'/Users/userView?uid='.$uid;
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$userInfo['account'];
    	$logo = 'http://'.$_SERVER['HTTP_HOST'].'/Public/images/logo.png';
    	$twodimcode = 'http://'.$_SERVER['HTTP_HOST'].$userInfo['twodimcode'];
    	$auth = array(L('auth_private'), L('auth_semi_public'), L('auth_public'));
    
    	load('extend');
    	$body = "<table>";
    	$body = $body. "<tr><td><p>Madame, Monsieur,</p><br /><p>Vous venez de créer un mémorial pour <strong>".$userInfo['lastname']." ".$userInfo['firstname']."</strong> à l’aide de notre site <strong>Offrez un Sourir Eternel</strong> et nous vous en remercions. Vous pouvez dès à présent y accéder et le partager à vos proches grâce au lien suivant : <strong><a href='$url'>$url</a></strong></p></td></tr>";
    	$body = $body. "<tr><td><p>Pour modifier les informations contenues dans le mémorial, nous vous invitons à vous rendre sur notre site et à saisir vos identifiants dans l’Espace Famille et Proches. </p></td></tr>";
    	$body = $body. "<tr><td>Nous vous rappelons que vous avez défini les informations d’accès suivantes :</td></tr>";
    	$body = $body. "<tr><td>Identifiant :<strong>". $userInfo['account']."</strong></td></tr>";
    	$body = $body. "<tr><td>Mot de passe :<strong>".$_SESSION['password_not_md5']."</strong></td></tr>";
    	$body = $body. "<tr><td>Accès : <strong>".$auth[$userInfo['visiable']]."</strong></td></tr>";
    	$body = $body. "<tr><td>Mot de passe d’accès : <strong>".$_SESSION['accesspwd_not_md5']."</strong></td></tr>";
    	$body = $body. "<tr><td>Indication :<strong>".$userInfo['pwdhint']."</strong></td></tr>";
    	$body = $body. "<tr><td><p>N’hésitez pas à nous contacter si vous avez la moindre question à l’adresse contact@unsourireternel.fr</p></td></tr>";
    	$body = $body. "<tr><td>Nous vous remercions pour votre confiance,</td></tr>";
    	$body = $body. "<tr><td>L’équipe Offrez-lui un Sourire Eternel.</td></tr>";
		$body = $body. "<tr><td><img src='$logo' style='width:200px;' /></td></tr>";

    	$body = $body. "</table>";
    
    	$toname = $userInfo['lastname'].'  '. $userInfo['firstname'];
    	//$subject = L('regist_email_subject');
		$subject = 'Bienvenue';
    	$result = think_send_mail($userInfo['email'], $toname, $subject, $body, false, $userInfo['twodimcode']);
		
    	if ($result === true) {
    		$_SESSION['password_not_md5'] = '';
    		$_SESSION['accesspwd_not_md5'] = '';
    	}
    	return $result;
    }
    
    public function deleteUsersByIds($ids) {
    	if (!isset($ids) || empty($ids)){
    		return false;
    	}
    	 
    	if(is_array($ids)){
    		if(count($ids) > 1) {
    			$w = 'uid in('.implode(',',$ids).')';
    		} else if (count($ids) == 1) {
    			$w = 'uid='.$ids[0];
    		}
    	}
    
    	return $this->where($w)->delete();
    }
}