<?php
// agency model
class CategoryModel extends Model {
      
	// In used
    public function getCategoryBriefList() {
    	$f = array('name', 'version', 'repository');
    	return $this->field($f)->select();
    }

    public function getAllCategoryName() {
    	$f = array('name');
    	return $this->field($f)->select();
    }
    
    public function getCategoryInfoByName($name) {
    	return $this->where(array('name'=>$name))->find();
    }
    
    public function getAllCategory() {
    	return $this->select();
    }
    
    public function getCount() {
    	//$w = array(statusid => 1);
    	return $this->count();
    }
}