<?php 
require '../vendor/autoload.php';

/**
 * The definition of the easy mart elastic search
 */
class Elastic
{
	private $elastic_client = null;
	
	function __construct(argument)
	{
		# code...
		$this->elastic_client = Elasticsearch\ClientBuilder::create()->build();
	}
}