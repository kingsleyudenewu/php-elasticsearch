<?php 
require '../vendor/autoload.php';
require_once 'config.php';

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
		$this->conn = new mysqli(DB_HOST, DB_USER, '', DB_NAME);

		if ($this->conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		} 
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

    public function update_node($id){
    	if(empty($id)) return 'Invalid ID selected';

    	$client = $this->elastic_client;
    	$result = $this->conn->query("SELECT products.title, products.description, products.sku, products.bulk_quantity, products.quantity, products.dimension, products.warranty, products.weight, products.barcode, categories.name as cat_name, categories.description as cat_description, sub_categories.name as sub_category_name, brands.name as brand_name from products JOIN categories ON products.category_id = categories.id JOIN sub_categories ON products.sub_category_id = sub_categories.id JOIN sub_category_types ON products.sub_category_type_id = sub_category_types.id JOIN brands ON products.brand_id = brands.id WHERE products.id = '{$id}'");

    	if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$params = [
				    'index' => 'products',
				    'type' => 'product',
				    '_id' => $row['id'],
				    'body' => $row
				];
			}
			$response = $client->update($params);
			return true;
		}
    }

    public function DeleteNode($id)
   	{
   		if(empty($id)) return 'Invalid ID selected';

       $client = $this->elastic_client;
       $params = [
       		'index' => 'products',
			'type' => 'product',
       		'id' => $id
       	];
       $responses = $client->delete($params);
       return true;
   	}
}