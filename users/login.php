<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate product object
include_once '../objects/users.php';
 
$database = new Database();
$db = $database->getConnection();
 
$User = new Users($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));



// make sure data is not empty
if( !empty($data->email) && !empty($data->password) ){ 


	$User->email = $data->email;
    $User->password = md5($data->password);

	$stmt = $User->login();
	$num  =  $stmt->rowCount();
 	
 	// echo '<pre>'; print_r($num); exit();

// check if more than 0 record found
if($num>0){

	    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

	        extract($row);
	        $User->id = $row['id'];

		   $auth_token = $User->tokanUpdate();
		   $current_user_id = $User->id;
    	}

    	if($auth_token) {

    		// set response code - 200 OK
		    http_response_code(200);
		 
		    // show products data in json format
		    echo json_encode(array('status'=> 1 , 'message' => 'get token successfully!', 'token' => $auth_token, 'current_user_id' => $current_user_id));
    	} else {
    		// set response code - 503 service unavailable
	        http_response_code(503);
	 
	        // tell the user
	        echo json_encode(array("message" => "Something went wrong try again!"));
    	}
 
		   
} else {

	 	// set response code - 503 service unavailable
        http_response_code(503);
 
        // tell the user
        echo json_encode(array("message" => "Unable to find user."));
}

} else {

	 // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to create user. Data is incomplete."));
 }