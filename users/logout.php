<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object file
include_once '../config/database.php';
include_once '../objects/users.php';
include_once '../objects/auth.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare User object
$User = new Users($db);
$Auth = new Auth($db);
 
// get User id
$data = json_decode(file_get_contents("php://input"));
 

// read User will be here
$Auth->current_user_id = $data->id;
$Auth->api_token = $data->token;

$checkIsSuperUser = $Auth->checkIsSuperUser();
$userExists = $checkIsSuperUser->rowCount();
if($userExists == 0) {
    // set response code - 503 service unavailable
    http_response_code(503);

    // tell the user
   echo json_encode(array("message" => "Don't have access!")); exit();
}

// set User id to be deleted
$User->id = $data->id;
 
// delete the User
if($User->logout()){
 
    // set response code - 200 ok
    http_response_code(200);
 
    // tell the user
    echo json_encode(array("message" => "User was deleted."));
}
 
// if unable to delete the User
else{
 
    // set response code - 503 service unavailable
    http_response_code(503);
 
    // tell the user
    echo json_encode(array("message" => "Unable to delete User."));
}
?>