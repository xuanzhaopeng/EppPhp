<?php
class Tree {
	/**
	 * 生成树型结构所需要的2维数组
	 * @var array
	 */
	public $arr = array();
	
	public $strTree = '';
	public $rootId;
	
	/**
	 * 构造函数，初始化类
	 * @param array 2维数组，例如：
	 * array(
	 *      1 => array('id'=>'1','pid'=>0,'name'=>''),
	 *      2 => array('id'=>'2','pid'=>1,'name'=>''),
	 *      3 => array('id'=>'3','pid'=>1,'name'=>''),
	 *      4 => array('id'=>'4','pid'=>1,'name'=>''),
	 *      5 => array('id'=>'5','pid'=>2,'name'=>''),
	 *      6 => array('id'=>'6','pid'=>3,'name'=>''),
	 *      7 => array('id'=>'7','pid'=>3,'name'=>'')
	 *      )
	 */
	public function init($arr=array(), $rootId) {
		$this->arr = $arr;
		$this->strTree = '';
		$this->rootId = $rootId;
		return is_array($arr);
	}
	
	/**
	 * 得到子级数组
	 * @param int
	 * @return array
	 */
	public function getChild($myid){
		$a = $newarr = array();
		if(is_array($this->arr)){
			foreach($this->arr as $id => $a){
				if($a['pid'] == $myid) $newarr[$id] = $a;
			}
		}
		return $newarr ? $newarr : false;
	}
	
	/**
	 * 得到ul li 型的树
	 */
	public function getTree($id) {
		
		$childs = $this->getChild($id);

		if (is_array($childs)) {
			$curr = array();
			if (isset($this->arr[$id])) {
				$curr = $this->arr[$id];
				$curr['conname'] = trim($curr['conname']);
				if(!empty($curr['conname']) && trim($curr['conname']) != '') {
					if($curr['gender'] == 0 && $curr['congender'] == 0) {
						$this->strTree = $this->strTree . "<li id='".$curr['id']."'><div class='female' style='float:left; '>".$curr['name']. "<br />".$curr['birthday'].'-'.$curr['deathday']."</div><div class='female' style='float:right;'>".$curr['conname']."<br />".$curr['conbirthday'].'-'.$curr['condeathday']."</div><ul>";
					}else if($curr['gender'] == 0 && $curr['congender'] == 1) {
						$this->strTree = $this->strTree . "<li id='".$curr['id']."'><div class='female' style='float:left; '>".$curr['name']. "<br />".$curr['birthday'].'-'.$curr['deathday']."</div><div class='male' style='float:right;'>".$curr['conname']."<br />".$curr['conbirthday'].'-'.$curr['condeathday']."</div><ul>";
					}else if($curr['gender'] == 1 && $curr['congender'] == 0) {
						$this->strTree = $this->strTree . "<li id='".$curr['id']."'><div class='male' style='float:left; '>".$curr['name']. "<br />".$curr['birthday'].'-'.$curr['deathday']."</div><div class='female' style='float:right;'>".$curr['conname']."<br />".$curr['conbirthday'].'-'.$curr['condeathday']."</div><ul>";
					}else {
						$this->strTree = $this->strTree . "<li id='".$curr['id']."'><div class='male' style='float:left; '>".$curr['name']. "<br />".$curr['birthday'].'-'.$curr['deathday']."</div><div class='male' style='float:right;'>".$curr['conname']."<br />".$curr['conbirthday'].'-'.$curr['condeathday']."</div><ul>";
					}
				} else {
					if ($curr['gender'] == 0) {
					$this->strTree = $this->strTree . "<li class='female' id='".$curr['id']."'>".$curr['name'].'<br />'.$curr['birthday'].'-'.$curr['deathday']."<ul>";
					} else {
						$this->strTree = $this->strTree . "<li class='male' id='".$curr['id']."'>".$curr['name'].'<br />'.$curr['birthday'].'-'.$curr['deathday']."<ul>";
					}
				}
				
			}
			
			foreach($childs as $cid => $child) {
				$this->getTree($cid);
			}

			$this->strTree = $this->strTree . '</ul></li>';
		} else {
			if (isset($this->arr[$id])) {
				$child = $this->arr[$id];
					if(!empty($child['conname']) && trim($child['conname']) != '') {
						if($child['gender'] == 0 && $child['congender'] == 0) {
							$this->strTree = $this->strTree . "<li id='".$child['id']."' ><div style='width:280px;'><div class='female' style='float:left; '>".$child['name']. "<br />".$child['birthday'].'-'.$child['deathday']."</div><div class='female' style='float:right;'>".$child['conname']."<br />".$child['conbirthday'].'-'.$child['condeathday']."</div></div></li>";
						}else if($child['gender'] == 0 && $child['congender'] == 1) {
							$this->strTree = $this->strTree . "<li id='".$child['id']."'><div style='width:280px;'><div class='female' style='float:left; '>".$child['name']. "<br />".$child['birthday'].'-'.$child['deathday']."</div><div class='male' style='float:right;'>".$child['conname']."<br />".$child['conbirthday'].'-'.$child['condeathday']."</div></div></li>";
						}else if($child['gender'] == 1 && $child['congender'] == 0) {
							$this->strTree = $this->strTree . "<li id='".$child['id']."'><div style='width:280px;'><div class='male' style='float:left; '>".$child['name']. "<br />".$child['birthday'].'-'.$child['deathday']."</div><div class='female' style='float:right;'>".$child['conname']."<br />".$child['conbirthday'].'-'.$child['condeathday']."</div></div></li>";
						}else {
							$this->strTree = $this->strTree . "<li id='".$child['id']."'><div style='width:280px;'><div class='male' style='float:left;  '>".$child['name']. "<br />".$child['birthday'].'-'.$child['deathday']."</div><div class='male' style='float:right;'>".$child['conname']."<br />".$child['conbirthday'].'-'.$child['condeathday']."</div></div></li>";
						}
					}else
					{
						if ($child['gender'] == 0) {
							$this->strTree = $this->strTree . "<li class='female' id='".$child['id']."'>".$child['name'].'<br />'.$child['birthday'].'-'.$child['deathday'].'</li>';
						} else {
							$this->strTree = $this->strTree . "<li class='male' id='".$child['id']."'>".$child['name'].'<br />'.$child['birthday'].'-'.$child['deathday'].'</li>';
						}
					}
				
			}
		}
	}
	
	public function getHtmlSeleteTree($id) {
		$childs = $this->getChild($id);
		if (is_array($childs)) {
			$curr = array();
			if (isset($this->arr[$id])) {
				$curr = $this->arr[$id];
				$this->strTree = $this->strTree. '<option></option>';
			}			
			foreach($childs as $cid => $child) {
				$this->getHtmlSeleteTree($cid);
			}
			$this->strTree = $this->strTree . '';
		} else {
		}
	}
	
	public function getStrTree() {
		return $this->strTree;
	}
}