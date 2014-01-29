<?php
// agency model
class SplModel extends Model {
      
	// In used
    public function getSplBriefList() {
    	$f = array('id', 'name');
    	return $this->field($f)->select();
    }

    public function getAllSplName() {
    	$f = array('name');
    	return $this->field($f)->select();
    }
    
    public function getSplInfoById($id) {
    	return $this->where(array('name'=>$id))->find();
    }
    
    public function getAllSpl() {
    	return $this->select();
    }
    
    public function getCount() {
    	//$w = array(statusid => 1);
    	return $this->count();
    }
}