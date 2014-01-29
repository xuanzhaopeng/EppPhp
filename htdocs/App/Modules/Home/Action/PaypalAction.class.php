<?php
class PaypalAction extends Action{
	public function checkout(){
		$uid = $this->_get('uid');
		
		if (empty($uid)) {
			$this->error(L('checkout_failed'));
		}
		
		$this->assign('uid', $uid);
		$this->assign('host', 'http://'.$_SERVER['HTTP_HOST']);
		$this->display();
	}
	public function notify() {	
		
		$req = 'cmd=_notify-validate';
		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		// post back to PayPal system to validate
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

		// assign posted variables to local variables
		$itemnumber = $_POST['item_number'];
		$uid = decryptUid($itemnumber);
		$data['uid'] = $uid;
		$data['test_ipn'] = $_POST['test_ipn'];
		$data['txn_id'] = $_POST['txn_id'];
		$data['txn_type'] = $_POST['txn_type'];
		$data['payer_email'] = $_POST['payer_email'];
		$data['first_name'] = $_POST['first_name'];
		$data['last_name'] = $_POST['last_name'];
		$data['handling_amount'] = $_POST['handling_amount'];
		$data['item_number'] = $itemnumber;
		$data['payment_type'] = $_POST['payment_type'];
		$data['payment_status'] = $_POST['payment_status'];
		$data['pending_reason'] = $_POST['pending_reason'];


		if (!$fp) {
			// HTTP ERROR
		} else {
			fputs ($fp, $header . $req);
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				if (strcmp ($res, "VERIFIED") == 0) {
					$Paypal = D('Paypal');
					$data['pending_reason'] = $res;
					if (!$Paypal->txnExisted($data['txn_id'])) {
						if (!$Paypal->add($data)) {
							echo $Paypal->getError();
						}
						if ($data['payment_status'] == "Completed") {
							$Users = D('Users');
							$result = $Users->checkoutComplete($uid);
							if ($result !== false) {
								$userInfo = $Users->getBasicUserInfoByUid($uid);
								$Users->sendEmail($userInfo);
							}
						}
					}else {
						if(!empty($data['txn_id'])){
							$previousstatus = $Paypal->getPaymentStatus($data['txn_id']);
							$condition['txn_id'] = $data['txn_id'];
							$condition['uid'] = $data['uid'];
							
							$Paypal->where($condition)->save($update);
							if($previousstatus['payment_status'] != "Completed" && $data['payment_status'] == "Completed") {
								$Users = D('Users');
								$result = $Users->checkoutComplete($uid);
								if ($result !== false) {
									$userInfo = $Users->getBasicUserInfoByUid($uid);
									$Users->sendEmail($userInfo);
								}
							}
						}
					}
					
				}else if (strcmp ($res, "INVALID") == 0) {
					// log for manual investigation
				}
			}
			fclose ($fp);
		}
		
	}
	
	public function thanks() {
		$itemNumber = $this->_get('item_number');
		$uid = decryptUid($itemNumber);
		$Users = D('Users');
		$userInfo = $Users->getBasicUserInfoByUid($uid);
		$this->assign('itemNumber', $userInfo['account']);
		$this->display();
	}
}

?>