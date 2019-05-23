<?php 
require_once 'config/Elastic.php';
if(isset($_POST['body'])){
	$insert_node = $elastic->insert_node($_POST['body']);
	echo $insert_node;
}