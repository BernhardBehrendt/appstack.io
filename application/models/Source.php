<?php
class Application_Model_Source extends Zend_Dom_Query{

	public function __construct($sLocation){
		if(!isset($sLocation)){
			return false;
		}

		parent::__construct($sLocation);
	}

	public function ddd(){

	}

}