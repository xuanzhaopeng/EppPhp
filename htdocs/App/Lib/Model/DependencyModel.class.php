<?php
// agency model
class DependencyModel extends Model {
      
	// In used
    public function getAllDependency() {
    	return $this->select();
    }
    
    public function getCount() {
    	//$w = array(statusid => 1);
    	return $this->count();
    }
}