<?php
class Application_Model_Formatphp
{
	public static function toPhp($data){
		Preprocessor_Header::setContentType('php');
		return serialize($data);
	}
}
?>