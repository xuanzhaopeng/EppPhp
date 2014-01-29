<?php
// agency model
class FeatureModel extends Model {
      
	// In used
    public function getFeatureBriefList() {
    	$f = array('name', 'version', 'repository');
    	return $this->field($f)->select();
    }

    public function getAllFeautreName() {
    	$f = array('name');
    	return $this->field($f)->select();
    }
    
    public function getFeatureInfoById($id) {
    	return $this->where(array('id'=>$id))->find();
    }
    
    public function getAllFeature() {
    	return $this->select();
    }
    
    public function getCount() {
    	//$w = array(statusid => 1);
    	return $this->count();
    }
}