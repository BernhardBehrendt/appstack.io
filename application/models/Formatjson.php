<?php
class Application_Model_Formatjson
{
	public static function toJson($data){
			Preprocessor_Header::setContentType('json');
		return json_encode($data);
	}
}
?>