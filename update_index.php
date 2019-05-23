<?php 
require_once 'config/Elastic.php';
if(isset($_REQUEST['q'])){
	$insert_node = $elastic->search($_REQUEST['q']);
	echo $insert_node;
}