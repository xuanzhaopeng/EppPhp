<?php
class PublicAction extends Action{

    public function login() {
		header('HTTP/1.1 200 OK');
    	$this->display();
    }
    
    public function createUser() {
		header('HTTP/1.1 200 OK');
    	$this->display();
    }
    
    public function searchUser()
    {
		header('HTTP/1.1 200 OK');
		$this->display();
    }
	
    public function mention() {
		header('HTTP/1.1 200 OK');
    	$this->display();
    }
	
    public function FAQ() {
		header('HTTP/1.1 200 OK');
    	$this->display();
    }
	
	public function sendMail(){
		$email = $this->_post('email');
		if (empty($email) || trim($email) == '') {
    		$this->error("Merci de renseigner votre email!");
		}
		$logo = 'http://'.$_SERVER['HTTP_HOST'].'/Public/images/logo.png';
		$clienttype = $this->_post('clienttype');
		if($clienttype == 0){
			$data['clienttype'] = 'Agence';
		} else {
			$data['clienttype'] = 'Client';
		}
		$data['telephone'] = $this->_post('telephone');
		$data['message'] = $this->_post('message');
		$data['email'] = $email;
		load('extend');
		$body = "<table><tr><td><img src='$logo' /></td></tr>";
    	$body = $body. "<tr><td><p>Bonjour,</p><p>Nous avons bien pris en compte votre demande d'informations sur notre site internet et nous y répondrons sous les plus brefs délais.</p><p>A très bientôt,</p></td></tr>";
    	$body = $body. "<tr><td>L'équipe OSE</td></tr>";
    	$body = $body. "<tr><td>www.unsourireternel.fr/</td></tr>";
		$body = $body. "<tr><td><p>Votre message est : <strong>".$data['message']."</p></td></tr>";
    	$body = $body. "</table>";
		
		$toname = "Cher ".$data['clienttype'];
		$subject = "Information: Contactez-Nous";
		$result = think_send_mail($data['email'], $toname, $subject, $body , true);
		if($result){
			$this->success("Votre message a bien été envoyé, nous y répondrons dans les plus brefs délais!","/Index");
		}else{
			$this->error("Send Failed");
		}

	}
    
    public function contactUs() {
		header('HTTP/1.1 200 OK');
    	$this->display();
    }
    
    //upload image
    public function uploadImg() {
    	if (!empty($_FILES)) {
    		$thumbPrefix = $this->_post('prefix','strip_tags','thumb_' );
    		$thumbW = $this->_post('thumbW', 'strip_tags', 150);
    		$thumbH = $this->_post('thumbH', 'strip_tags', 150);
    		
    		$this->_uploadImg($thumbPrefix, $thumbW, $thumbH);
    	}
    }
    
    //upload image
    protected function _uploadImg($thumbPrefix,$thumbW,$thumbH) {
    	import('@.ORG.Util.UploadFile');
    	$upload = new UploadFile();
    	$upload->maxSize            = 20971520;
    	$upload->allowExts          = explode(',', 'jpg,gif,png,jpeg');
    	$upload->savePath           = './Uploads/';
    	$upload->thumb              = true;
    	$upload->imageClassPath     = '@.ORG.Util.Image';
    	$upload->thumbPrefix        = $thumbPrefix;
    	$upload->thumbMaxWidth      = $thumbW;
    	$upload->thumbMaxHeight     = $thumbH;
    	$upload->saveRule           = 'uniqid';
    	$upload->thumbRemoveOrigin  = false; //delete the original image

    	if (!$upload->upload()) {
    		//$this->error($upload->getErrorMsg());
    		$msg = $upload->getErrorMsg();
    		if ($msg == '上传文件类型不允许') {
    			$msg = L('file_type_is_not_allowed');
    		} else if($msg='上传文件大小不符！') {
    			$msg = L('filw_size_discrepancy');
    		}
			header('HTTP/1.1 200 OK');
    		echo json_encode(array(status => 0, msg=> $msg));
    		exit;
    	} else {
    		$uploadList = $upload->getUploadFileInfo();
    	}
    	if ($uploadList !== false) {
			header('HTTP/1.1 200 OK');
    		echo json_encode(array(status => 1, imgName =>$uploadList[0]['savename']));
    	} else {
			header('HTTP/1.1 200 OK');
    		echo json_encode(array(status => 0));
    	}
    }
    
    public function mobileUploadImg() {
    	if (!empty($_FILES)) {
    		$thumbPrefix = $this->_post('prefix','strip_tags','thumb_' );
    		$thumbW = $this->_post('thumbW', 'strip_tags', 150);
    		$thumbH = $this->_post('thumbH', 'strip_tags', 150);
    		$encryptid = $this->_post('uid');
    		$uid = decryptUid($encryptid);
    	
    		import('@.ORG.Util.UploadFile');
    		$upload = new UploadFile();
    		$upload->maxSize            = 20971520;
    		$upload->allowExts          = explode(',', 'jpg,gif,png,jpeg');
    		$upload->savePath           = './Uploads/';
    		$upload->thumb              = true;
    		$upload->imageClassPath     = '@.ORG.Util.Image';
    		$upload->thumbPrefix        = $thumbPrefix;
    		$upload->thumbMaxWidth      = $thumbW;
    		$upload->thumbMaxHeight     = $thumbH;
    		$upload->saveRule           = 'uniqid';
    		$upload->thumbRemoveOrigin  = false; //delete the original image
    		
    		if (!$upload->upload()) {
    			//$this->error($upload->getErrorMsg());
    			$msg = $upload->getErrorMsg();
    			if ($msg == '上传文件类型不允许') {
    				$msg = L('file_type_is_not_allowed');
    			} else if($msg='上传文件大小不符！') {
    				$msg = L('filw_size_discrepancy');
    			}
				header('HTTP/1.1 200 OK');
    			echo json_encode(array(status => 0, msg=> $msg));
    			exit;
    		} else {
    			$uploadList = $upload->getUploadFileInfo();
    		}
    		if ($list !== false) {
    			$p = M('UserPictures');
    			$data['uid'] = $uid;
    			$data['name'] = '/Uploads/'.$uploadList[0]['savename'];
    			$data['createdtime'] = date('Y-m-d H:i:s', time());
    			$p->add($data);
				header('HTTP/1.1 200 OK');
    			echo json_encode(array(status => 1, imgName =>$uploadList[0]['savename']));
    		} else {
				header('HTTP/1.1 200 OK');
    			echo json_encode(array(status => 0));
    		}
    	}
    }
    
    //logout
    public function logout() {
    	if(isset($_SESSION[C('USER_AUTH_KEY')])|| isset($_SESSION[C('AGENCY_AUTH_KEY')])) {
    		unset($_SESSION[C('USER_AUTH_KEY')]);
    		unset($_SESSION[C('AGENCY_AUTH_KEY')]);
    		unset($_SESSION[C('USER_GROUP')]);
			unset($_SESSION['agencyname']);
			unset($_SESSION['clientname']);
    		unset($_SESSION);
    		session_destroy();
    		$this->redirect('/Index/index');
    	} else {
    		$this->redirect('/Index/index');
    	}
    }
    
    public function whoweare() {
		//header('HTTP/1.1 200 OK');
    	$this->display();
    }
}