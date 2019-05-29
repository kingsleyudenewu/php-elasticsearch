<?php 
require_once 'config/Elastic.php';
$rawData = file_get_contents("php://input");
// this returns null if not valid json
$variable = json_decode($rawData);
$insert_node = $elastic->insert_node($variable);
echo $insert_node;