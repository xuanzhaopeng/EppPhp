<?php
// agency model
class AgencyModel extends Model {
    public $_validate	=	array(
    	array('email','require','{%email_require}'),
    	array('name','require','{%agency_name_require}'),
        );
    
    public function emailExisted($email) {
    	$w = array('email' => $email);
    	$f = array('id');
    	$result = $this->where($w)->field($f)->find();
    	
    	if(!empty($result)) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    public function getAgencyEmailById($id) {
    	$w = array('id' => $id);
    	$f = array('email');
    	$result = $this->where($w)->field($f)->find();	
    	return $result;
    }
    
    public function getAgencyBriefInfoById($id) {
    	$w = array('id' => $id);
    	$f = array('id', 'name', 'email', 'phone', 'logo','siret','network', 'road_name', 'city', 'postcode', 'firstname','lastname', 'contactPhone', 'contactEmail');
    	$result = $this->where($w)->field($f)->find();
    	
    	return $result;
    }
    
    public function getAgencyBriefList() {
    	$f = array('id', 'name', 'email', 'phone', 'road_name', 'city', 'firstname','lastname', 'createdtime');
    	return $this->field($f)->select();
    }
    
    
    public function getAllAgencyName() {
    	$f = array('name');
    	//$w = array('statusid' => 1, 'confirmid' => 1);
    	return $this->field($f)->select();
    }
    
    public function getAgencyInfoById($id) {
    	return $this->where(array('id'=>$id))->find();
    }
    
    public function getAllAgency() {
    	return $this->select();
    }
    
    public function getCount() {
    	//$w = array(statusid => 1);
    	return $this->count();
    }
    
    public function deleteAgencyByIds($ids) {
    	if (!isset($ids) || empty($ids)){
    		return false;
    	}
    	
    	if(is_array($ids)){
    		if(count($ids) > 1) {
    			$w = 'id in('.implode(',',$ids).')';
    		} else if (count($ids) == 1) {
    			$w = 'id='.$ids[0];
    		}
    	}

    	return $this->where($w)->delete();
    }
}