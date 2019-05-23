<?php 
require '../vendor/autoload.php';

/**
 * The definition of the easy mart elastic search
 */
class Elastic
{
	private $elastic_client = null;
	private $conn = null;
	
	function __construct(argument)
	{
		# code...
		$this->elastic_client = Elasticsearch\ClientBuilder::create()->build();
		$this->conn = 
	}

	public function Mapping(){
	    $params = [
	        'index' => 'products',
	        'body' => [
	            'mappings' => [
	                'product' => [
	                    'properties' => [
	                        'id' => [
	                            'type' => 'integer'
	                         
	                        ],
	                        'title' => [
	                            'type' => 'string'
	                         
	                        ],
	                        'description' => [
	                            'type' => 'string'
	                         
	                        ],
	                        'sku' => [
	                            'type' => 'string'
	                         
	                        ],
	                        'quantity' => [
	                            'type' => 'integer'
	                         
	                        ],
	                        'bulk_quantity' => [
	                            'type' => 'integer'
	                         
	                        ],
	                        'warranty' => [
	                            'type' => 'string'
	                         
	                        ],
	                        'dimension' => [
	                            'type' => 'string'
	                         
	                        ],
	                        'weight' => [
	                            'type' => 'string'
	                         
	                        ],
	                        'barcode' => [
	                            'type' => 'string'
	                         
	                        ],
	                        'cat_name' => [
	                            'type' => 'string'
	                         
	                        ],
	                        'cat_description' => [
	                            'type' => 'string'
	                         
	                        ],
	                        'sub_category_name' => [
	                            'type' => 'string'
	                         
	                        ],
	                        'brand_name' => [
	                            'type' => 'string'
	                         
	                        ],

	                    ]
	                ]
	            ]
	        ]
	    ];
       $this->elastic_client->indices()->create($params);
    }

    public function insert_node($data){
    	if(!is_array($data)) return 'Data must be an array';
    	$client = $this->elastic_client;
    	$this->Mapping();
    	$params = [
		    'index' => 'products',
		    'type' => 'product',
		    '_id' => $data['id'],
		    'body' => $data
		];
		$response = $client->index($params);
		return true;

    }

    public function update_node($id, $con){

    }
}