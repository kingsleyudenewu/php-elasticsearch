<?php 
require_once 'config/Elastic.php';
if(isset($_REQUEST['q'])){
	$insert_node = $elastic->update_node($_REQUEST['q']);
	echo $insert_node;
}