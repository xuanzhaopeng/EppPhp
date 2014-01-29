<?php
class PublicAction extends Action {
	//login page
	public function login() {
		if(!isset($_SESSION[C('ADMIN_AUTH_KEY')])) {
			$this->display();
		}else{
			$this->redirect('/Admin/Public/index');
		}
	}
	
	//home page
	public function index() {
		if(isset($_SESSION[C('ADMIN_AUTH_KEY')])) {
			$this->display();
		}else{
			$this->redirect('/Admin/Public/login');
		}
	}
	
    //logout
    public function logout() {
        unset($_SESSION[C('ADMIN_AUTH_KEY')]);
        unset($_SESSION);
   		session_destroy();
        $this->redirect('/Admin/Public/login');
    }

    //login check
    public function checkLogin() {
        if(empty($_POST['account'])) {
			header("HTTP/1.0 200 OK");
        	jsonecho(0, L('pls_enter_account'));
        }elseif (empty($_POST['password'])){
			header("HTTP/1.0 200 OK");
            jsonecho(0, L('wrong_password'));
        }
        
        $map = array();
        $map['account']	= $_POST['account'];
        $map["status"]	=	array('gt',0);
        
        //if(session('verify') != md5($_POST['verify'])) {
        //	jsonecho(L('verify_wrong'));
        //}
        
        $authInfo = M('Admin')->where($map)->find();
        if(false === $authInfo) {
			header("HTTP/1.0 200 OK");
            jsonecho(0, L('no_account'));
        }
        if($authInfo['password'] != md5($_POST['password'])) {
			header("HTTP/1.0 200 OK");
            jsonecho(0, L('wrong_password'));
        }
            $_SESSION[C('ADMIN_AUTH_KEY')] =	$authInfo['id'];
            $_SESSION['account'] = $authInfo['account']; 
            $_SESSION['loginUserName'] = $authInfo['username'];
            $_SESSION['email'] = $authInfo['email'];
            $_SESSION['lastLoginTime'] = $authInfo['last_login_time'];
            $_SESSION['login_count'] = $authInfo['login_count'];
            
            //save login information
            $User =	M('Admin');
            $ip	= get_client_ip();
            $time =	time();
            $data = array();
            $data['id']	= $authInfo['id'];
            $data['last_login_time'] = $time;
            $data['login_count'] = array('exp','login_count+1');
            $data['last_login_ip'] = $ip;
			$condition['id'] = $data['id'];
			$User->where($condition)->data($data)->save();
			header("HTTP/1.0 200 OK");
            jsonecho(1);
        
    }
    
    //upload image
    public function uploadImg() {
    	if (!empty($_FILES)) {
    		$thumbPrefix = $_POST['prefix'];
    		$thumbW = $_POST['thumbW'];
    		$thumbH = $_POST['thumbH'];
    		$this->_uploadImg($thumbPrefix, $thumbW, $thumbH);
    	}
    }
    
    //upload image
    protected function _uploadImg($thumbPrefix,$thumbW,$thumbH) {
    	import('@.ORG.Util.UploadFile');
    	$upload = new UploadFile();
    	$upload->maxSize            = 3292200;
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
    		$this->error($upload->getErrorMsg());
    	} else {
    		$uploadList = $upload->getUploadFileInfo();
    	}
    	if ($list !== false) {
			header("HTTP/1.0 200 OK");
    		echo json_encode(array(status => 1, imgName =>$uploadList[0]['savename']));
    	} else {
			header("HTTP/1.0 200 OK");
    		echo json_encode(array(status => 0));
    	}
    }
    
   

    public function profile() {
        $this->checkUser();
        $User	 =	 M("User");
        $vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
        $this->assign('vo',$vo);
        $this->display();
    }

    public function verify() {
        $type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
        import("ORG.Util.Image");
        Image::buildImageVerify(4,1,$type);
    }
}