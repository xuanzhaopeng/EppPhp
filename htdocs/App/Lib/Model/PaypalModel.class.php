<?php
// merchant model
class PaypalModel extends Model {
//    public $_validate	=	array(
//    	array('email','require','{%email_require}'),
//    	array('password','require','{%password_require}'),
//        );
    
//	public $_validate = array(
//		array('account','require','{account_require}'),
//		array('password','require','{%password_require}')
//	);
	
	
	public function txnExisted($txn_id) {
		$w = array('txn_id' => $txn_id);
    	$f = array('uid');
    	$result = $this->where($w)->field($field)->find();

    	if (empty($result)) {
    		return false;
    	} else {
    		return true;
    	}
	}
	
	public function getPaymentStatus($txn_id) {
		$w = array('txn_id' => $txn_id);
    	$f = array('payment_status');
    	$result = $this->where($w)->field($field)->find();
    	return $result;
	}
}