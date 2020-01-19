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

// make sure data is not empty
if( !empty($data->firstname) && !empty($data->lastname) && !empty($data->email) && !empty($data->password)   && !empty($data->current_user_id)  && !empty($data->token) ) {
 
    // set user property values
    $User->firstName = $data->firstname;
    $User->lastName = $data->lastname;
    $User->email = $data->email;
    $User->password = md5($data->password);
    $Auth->current_user_id = $data->current_user_id;
    $Auth->api_token = $data->token;
    $Auth->opretion_name = 'creating user';
    

    $checkIsSuperUser = $Auth->checkIsSuperUser();
    $userExists = $checkIsSuperUser->rowCount();
    if ($userExists > 0) {

        
        // create the user
        $last_id = $User->create();
        if($last_id){
     
            // set response code - 201 created
            http_response_code(201);

            $Auth->new_user_id = $last_id;

            // echo '<pre>'; print_r($Auth); exit();

            $Auth->createLog();
     
            // tell the user
            echo json_encode(array('status'=> 1 , 'message' => 'User get created successfully!'));
        }
     
        // if unable to create the user, tell the user
        else{
     
            // set response code - 503 service unavailable
            http_response_code(503);
     
            // tell the user
            echo json_encode(array("message" => "Unable to create user."));
        }
    } else {
         // set response code - 503 service unavailable
        http_response_code(503);
 
        // tell the user
        echo json_encode(array("message" => "Unable to Authenticate user."));
    }
    

} else { // tell the user data is incomplete
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to create user. Data is incomplete."));
}
?>