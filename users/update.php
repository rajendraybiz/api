<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/users.php';
include_once '../objects/auth.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare User object
$User = new Users($db);
$Auth = new Auth($db);

 
// get id of User to be edited
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

 
// set ID property of User to be edited
$User->id = $data->id;
 
// set User property values
$User->firstName = $data->firstname;
$User->lastName = $data->lastname;
$User->Status = $data->status;
$User->email = $data->email;
$Auth->current_user_id = $data->current_user_id;
$Auth->api_token = $data->token;
$Auth->opretion_name = 'update user';
 
// update the User
if($User->update()){
 
    // set response code - 200 ok
    http_response_code(200);

     $Auth->new_user_id = $data->id;

     $Auth->createLog();
 
    // tell the user
    echo json_encode(array('status'=> 1 , 'message' => 'User was updated.'));
} else { // if unable to update the User, tell the user
 
    // set response code - 503 service unavailable
    http_response_code(503);
 
    // tell the user
    echo json_encode(array("message" => "Unable to update User."));
}
?>