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
include_once '../objects/auth.php';
 
$database = new Database();
$db = $database->getConnection();
 
$User = new Users($db);
$Auth = new Auth($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));

// read User will be here
$Auth->current_user_id = $data->current_user_id;
$Auth->api_token = $data->token;

$checkIsSuperUser = $Auth->checkIsSuperUser();
$userExists = $checkIsSuperUser->rowCount();
if($userExists == 0) {
    // set response code - 503 service unavailable
    http_response_code(503);

    // tell the user
    echo json_encode(array("message" => "Don't have access!")); exit();
}


// query User
$stmt = $User->audit();
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // products array
    $Users_arr=array();
    $i = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    	$i++;
        extract($row);

        $user_item=array(
        	'sr' => $i,
            "email" => $email,
            "opretion_name" => $opretion_name,
            "created_date" => $created_date,
            "new_user_email" => $new_user_email,
        );
 
        $Users_arr[] =  $user_item;
    }
 
    // set response code - 200 OK
    http_response_code(200);
 
    // show products data in json format
    echo json_encode($Users_arr);
}
 
// no products found will be here

else{
 
    // set response code - 404 Not found
    http_response_code(404);
 
    // tell the user no products found
    echo json_encode(
        array("message" => "No User found.")
    );
}

