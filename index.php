<?php
require_once 'config/Elastic.php';
//Drop the index if it exist
$elastic->drop_index('');
echo $elastic->insertData();

