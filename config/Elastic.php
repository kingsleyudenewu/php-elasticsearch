<?php 
require 'vendor/autoload.php';
require_once 'config.php';

/**
 * The definition of the easy mart elastic search
 */
class Elastic
{
	private $elastic_client = null;
	private $conn = null;
	
	function __construct()
	{
		# code...
        $this->conn = new mysqli(DB_HOST, DB_USER, '', DB_NAME);
		$this->elastic_client = Elasticsearch\ClientBuilder::create()->build();


		if ($this->conn->connect_error) {
		    die("Connection failed: " . $this->conn->connect_error);
		} 
	}

    public function drop_index($data){
        if(empty($data)) return 'Invalid index selected';
        $client = $this->elastic_client;
        $response = $client->indices()->delete($data);
        return $response;
    }

    private function create_index(){
        $client = $this->elastic_client;
        $indexParams = array();
        $indexParams['index'] = INDEX; // index is called products

        // be sure that the index doesn't already exist
        if ( !$client->indices()->exists($indexParams) ) {
            // create index products
            $client->indices()->create($indexParams);
        }
    }

    public function insertData(){
        $client = $this->elastic_client;
        $this->create_index();

        $result = $this->conn->query("SELECT products.id, products.title, products.description, products.sku, products.bulk_quantity, products.quantity, products.dimension, products.warranty, products.weight, products.barcode, categories.name as cat_name, categories.description as cat_description, sub_categories.name as sub_category_name, brands.name as brand_name from products JOIN categories ON products.category_id = categories.id JOIN sub_categories ON products.sub_category_id = sub_categories.id JOIN sub_category_types ON products.sub_category_type_id = sub_category_types.id JOIN brands ON products.brand_id = brands.id");

        while ($row = $result->fetch_assoc())
        {
            $params['body'][] = array(
                'index' => array(
                    '_index' => INDEX,
                    '_type' => TYPE,
                    '_id' => $row['id'],
                ) ,
            );
            $params['body'][] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'sku' => $row['sku'],
                'quantity' => $row['quantity'],
                'bulk_quantity' => $row['bulk_quantity'],
                'dimension' => $row['dimension'],
                'warranty' => $row['warranty'],
                'weight' => $row['weight'],
                'barcode' => $row['barcode'],
                'cat_name' => $row['cat_name'],
                'cat_description' => $row['cat_description'],
                'sub_category_name' => $row['sub_category_name'],
                'brand_name' => $row['brand_name']
            ];
        }
        $responses = $client->bulk($params);
        return json_encode($responses);
    }

    public function insert_node($data){
    	if(!is_array($data)) return 'Data must be an array';

    	$client = $this->elastic_client;
    	$params = [
		    'index' => INDEX,
		    'type' => TYPE,
            'id' => $data[0]->id,
		    'body' => $data[0]
		];
		$response = $client->index($params);
		return json_encode($response);
    }

    public function update_node($id){
    	if(empty($id)) return 'Invalid ID selected';

    	$client = $this->elastic_client;
    	$result = $this->conn->query("SELECT products.id, products.title, products.description, products.sku, products.bulk_quantity, products.quantity, products.dimension, products.warranty, products.weight, products.barcode, categories.name as cat_name, categories.description as cat_description, sub_categories.name as sub_category_name, brands.name as brand_name from products JOIN categories ON products.category_id = categories.id JOIN sub_categories ON products.sub_category_id = sub_categories.id JOIN sub_category_types ON products.sub_category_type_id = sub_category_types.id JOIN brands ON products.brand_id = brands.id WHERE products.id = '{$id}'");

    	if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$params = [
				    'index' => INDEX,
				    'type' => TYPE,
				    'body' => $row
				];
			}
			$response = $client->update($params);
            return json_encode($response);
		}
    }



    public function delete_node($id)
   	{
   		if(empty($id)) return 'Invalid ID selected';

       $client = $this->elastic_client;
       $params = [
       		'index' => INDEX,
			'type' => TYPE,
       		'id' => $id
       	];
       $responses = $client->delete($params);
        return json_encode($responses);
   	}

   	public function delete_many_node($id){
   		if(!is_array($id)) return 'Array needed and not text';

   		$client = $this->elastic_client;
   		foreach ($id as $key => $value) {
   			# code...
   			$params = [
	       		'index' => INDEX,
				'type' => TYPE,
	       		'id' => $value
	       	];
	        $responses = $client->delete($params);
            return json_encode($responses);
   		}
   	}

   	public function search($query){
   		if(empty($query)) return 'Invalid query selected';

   		$client = $this->elastic_client;
   		$result = [];
   		$i = 0;
        $params = [
            'index' => INDEX,
            'type' => TYPE,
            'body' => [
                'query' =>[
                    'multi_match' => [
                        'query' => $query,
//                        'type' => 'best_fields',
                    //The phrase_prefix types behave just like best_fields, but they use a match_phrase_prefix
                        // query instead of a match query
                        'type' => 'phrase_prefix',
                        'fields' => ['title', 'description', 'warranty', 'sku', 'barcode', 'cat_name', 'cat_description', 'sub_category_name', 'brand_name']
                    ]
                ]
            ]
        ];



		// Return the response of the search
		$response = $client->search($params);

		$hits = sizeof($response['hits']['hits']);
       	$hit = $response['hits']['hits'];
       	$result['searchfound'] = $hits;
       	while ($i < $hits) {
           $result['result'][$i] = $response['hits']['hits'][$i]['_source'];
           $result['result'][$i]['_id'] = $response['hits']['hits'][$i]['_id'];
           $i++;
       	}
       	return json_encode($result);
   	}
   	public function get_index($id){
        $client = $this->elastic_client;
        $params = [
            'index' => INDEX,
            'type' => TYPE,
            'id' => $id
        ];

        $response = $client->get($params);
        return json_encode($response);
    }
}

$elastic = new Elastic();