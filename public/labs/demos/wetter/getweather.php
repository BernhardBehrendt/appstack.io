<?php
if(isset($_GET['in'])){
	echo file_get_contents('http://www.google.com/ig/api?weather='.$_GET['in']);
}else{
	echo '<?xml version="1.0"?>';
}
?>