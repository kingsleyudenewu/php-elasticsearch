<?php
require_once 'config/Elastic.php';
//Drop the index if it exist
//$deleteParams['index'] = INDEX;
//echo $elastic->drop_index($deleteParams);
//echo $elastic->insertData();

echo $elastic->insertData();


