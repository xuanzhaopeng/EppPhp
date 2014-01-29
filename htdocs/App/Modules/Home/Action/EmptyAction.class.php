<?php
class EmptyAction extends Action{
    public function index(){
    	$account = $_GET["_URL_"][0]; 
		$Users = D('Users');
		$userInfo = $Users->getBasicUserInfoByAccount($account);
		if(!$_SESSION[C('USER_GROUP')]){
			$_SESSION[C('USER_GROUP')] = 'visiter';
		}
		$this->assign('hideRetourBtn', '1');
		if (!empty($userInfo)) {
			$this->redirect2('/Users/userView?account='.$account);
		} else {
			$this->redirect2('/Index');
		}
    }
}
?>