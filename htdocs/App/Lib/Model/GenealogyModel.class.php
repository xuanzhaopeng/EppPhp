<?php
// merchant model
class GenealogyModel extends Model {
    public $_validate	=	array(
    	array('uid','require','{%operation_failure}'),
    	array('pid','require','{%operation_failure}'),
        );
    
    public function getGenTree($data) {
    	import("@.ORG.Util.Tree");
    	$tree = new Tree();
    		
    	$tree->init($data, 0);
    	$tree->getTree(0);
    	return $tree->getStrTree();
    }
    
    public function getGenData($uid) {
    	$Genealogy = M('Genealogy');
    	$w = array('uid' => $uid);
    	$f = array('id', 'uid', 'pid', 'firstname','secondname', 'lastname', 'relationship', 'gender','avatar','birthday','deathday');
    	$r = $Genealogy->where($w)->field($f)->select();
    	$data = array();
    	foreach ($r as $v) {
    		$tmp = array();
    		$tmp['id'] = $v['id'];
    		$tmp['pid'] = $v['pid'];
    		$tmp['name'] = $v['lastname'].' '.$v['firstname'].' '.$v['secondname'];
    		$tmp['gender'] = $v['gender'];
    		$tmp['birthday'] = substr($v['birthday'], 0, 11);
    		$tmp['deathday'] =substr($v['deathday'], 0, 11);
			
			$tmp['conname'] = $v['conlastname'].' '.$v['confirstname'].' '.$v['consecondname'];
    		$tmp['congender'] = $v['congender'];
    		$tmp['conbirthday'] = substr($v['conbirthday'], 0, 11);
    		$tmp['condeathday'] =substr($v['condeathday'], 0, 11);
    		$data[$v['id']] = $tmp;
    	}
    	
    	return $data;
    }
}