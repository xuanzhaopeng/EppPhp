<?php
class IndexAction extends Action {
	public function index() {
		$this->redirect('/Admin/Public/login');
	}
}