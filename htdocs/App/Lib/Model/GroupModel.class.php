<?php
// groups model
class GroupModel extends Model {
    
    public function nameExisted($name) {
    	$w = array('name' => $name);
    	$f = array('id');
    	$result = $this->where($w)->field($f)->find();
    	
    	if(!empty($result)) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    public function getGroupNameById($id) {
    	$w = array('id' => $id);
    	$f = array('name');
    	$result = $this->where($w)->field($f)->find();	
    	return $result;
    }
    
    public function getGroupBriefInfoById($id) {
    	$w = array('id' => $id);
    	$f = array('id', 'name', 'description');
    	$result = $this->where($w)->field($f)->find();
    	
    	return $result;
    }
    
    public function getGroupBriefList() {
    	$f = array('id', 'name', 'description');
    	return $this->field($f)->select();
    }
    
    
    public function getAllGroupName() {
    	$f = array('name');
    	//$w = array('statusid' => 1, 'confirmid' => 1);
    	return $this->field($f)->select();
    }
    
    public function getGroupInfoById($id) {
    	return $this->where(array('id'=>$id))->find();
    }
    
    public function getAllGroup() {
    	return $this->select();
    }
    
    public function getCount() {
    	//$w = array(statusid => 1);
    	return $this->count();
    }
    
    public function deleteGroupsByIds($ids) {
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