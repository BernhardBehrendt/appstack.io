<?php
class Application_Model_Formatjsonp
{
	public static function toJsonp($data){
		Preprocessor_Header::setContentType('json');
		return json_encode($data);
	}
}
?>