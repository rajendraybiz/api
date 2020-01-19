<?php
class Users{
 
    // database connection and table name
    private $conn;
    private $table_name = "users";
    // object properties
    public $id;
    public $firstName;
    public $lastName;
    public $email;
    public $Status;
    public $password;
    public $api_token;
    public $created_at;
    public $updated_at;
 
    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }


function login () {

    $query = "select id, email, password from " . $this->table_name . " where email = '". $this->email . "' and password = '" . $this->password ."'" ;
    // prepare query
    $stmt = $this->conn->prepare($query);
    // execute query
    $stmt->execute();
 
    return $stmt;
}


function tokanUpdate () {

     // update query
    $query = "UPDATE " . $this->table_name . " SET  api_token=:api_token, updated_at=:updated_at WHERE id = :id";
 
    // prepare query statement
    $stmt = $this->conn->prepare($query);
 
    $this->api_token = rand();
    $this->id = $this->id;
    $updated_at = date('Y-m-d H:i:s');
    // bind new values 
    
    $stmt->bindParam(":api_token", $this->api_token);
    $stmt->bindParam(":updated_at", $updated_at);
    $stmt->bindParam(':id', $this->id);
 
    // execute the query
    if($stmt->execute()){
        return $this->api_token;
    }
 
    return false;
}


    // create product
function create(){
 
    // query to insert record
    $query = "INSERT INTO  " . $this->table_name . " SET firstName=:firstName, lastName=:lastName, email=:email, Status=:Status, password=:password, api_token=:api_token, created_at=:created_at, updated_at=:updated_at";
 
    // prepare query
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    $this->firstName  = $this->firstName;
    $this->lastName = $this->lastName;
    $this->email = $this->email;
    $this->Status = 'Active';
    $this->password = md5($this->password);
    $this->api_token = '';
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
 
    // bind values
    $stmt->bindParam(":firstName", $this->firstName);
    $stmt->bindParam(":lastName", $this->lastName);
    $stmt->bindParam(":email", $this->email);
    $stmt->bindParam(":Status", $this->Status);
    $stmt->bindParam(":password", $this->password);
    $stmt->bindParam(":api_token", $this->api_token);
    $stmt->bindParam(":created_at", $created_at);
    $stmt->bindParam(":updated_at", $updated_at);
    

    // execute query
    if($stmt->execute()){        
        return $this->conn->lastInsertId();
    }
 
    return false;
     
}


function read () {

    $query = "select id, firstName, lastName, email, Status  from " . $this->table_name . " where Status != 'super_user'" ;
    // prepare query
    $stmt = $this->conn->prepare($query);
    // execute query
    $stmt->execute();
 
    return $stmt;
}


function audit () {

    $query = "SELECT ula.user_log_activity_id, ula.new_user_id, u.email, ula.opretion_name, ula.created_date, (SELECT email from users WHERE id = ula.new_user_id) as new_user_email FROM user_log_activity AS ula JOIN users AS u ON ula.created_by = u.id" ;
    // prepare query
    $stmt = $this->conn->prepare($query);
    // execute query
    $stmt->execute();
 
    return $stmt;
}




// update the product
function update(){
 
    // update query
    $query = "UPDATE " . $this->table_name . " SET firstName=:firstName, lastName=:lastName, email=:email, Status=:Status, api_token=:api_token,  updated_at=:updated_at WHERE id = :id";
 
    // prepare query statement
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    $this->firstName  = $this->firstName;
    $this->lastName = $this->lastName;
    $this->email = $this->email;
    $this->Status = $this->Status;
    $this->api_token = '';
    $this->id = $this->id;
    $updated_at = date('Y-m-d H:i:s');
 
    // bind new values 
    $stmt->bindParam(":firstName", $this->firstName);
    $stmt->bindParam(":lastName", $this->lastName);
    $stmt->bindParam(":email", $this->email);
    $stmt->bindParam(":Status", $this->Status);
    $stmt->bindParam(":api_token", $this->api_token);
    $stmt->bindParam(":updated_at", $updated_at);
    $stmt->bindParam(':id', $this->id);
 
    // execute the query
    if($stmt->execute()){
        return true;
    }
 
    return false;
}


// delete the product
function delete(){
 
    // delete query
    $query = "DELETE FROM " . $this->table_name . " WHERE id = ".$this->id;
 
    // prepare query
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    $this->id = $this->id;
 
    // bind id of record to delete
    $stmt->bindParam(":id", $this->id);
 
    // execute query
    if($stmt->execute()){
        return true;
    }
 
    return false;
     
}

function logout() {
    // update query
    $query = "UPDATE " . $this->table_name . " SET api_token=:api_token  WHERE id = :id";
 
    // prepare query statement
    $stmt = $this->conn->prepare($query);
 
    // sanitize    
    $this->api_token = '';
    $this->id = $this->id;
    $updated_at = date('Y-m-d H:i:s');
 
    // bind new values
    $stmt->bindParam(":api_token", $this->api_token);
    $stmt->bindParam(':id', $this->id);
 
    // execute the query
    if($stmt->execute()){
        return true;
    }
 
    return false;
}


}
?>