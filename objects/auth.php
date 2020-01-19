<?php

class Auth{ 

	private $conn;
    private $table_name = "users";
    private $table_log_name = "user_log_activity";

	public $api_token;
    public $current_user_id;
    public $new_user_id;
    public $opretion_name;
    public $created_date;
    public $created_by;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

	function checkIsSuperUser() {

	    // $query = "select id, email, password from " . $this->table_name . " where api_token = '55555555' and id = '555'" ;
	    $query = "select id, email, password from " . $this->table_name . " where api_token = '". $this->api_token . "' and id = '" . $this->current_user_id ."'" ;
	    // prepare query
	    $stmt = $this->conn->prepare($query);
	    // execute query
	    $stmt->execute();
	    
	    return $stmt;
	}


	function createLog() {
 
	    // query to insert record
	    $query = "INSERT INTO  " . $this->table_log_name . " SET opretion_name=:opretion_name, new_user_id=:new_user_id, created_date=:created_date, created_by=:created_by";
	 
	    // prepare query
	    $stmt = $this->conn->prepare($query);
	 
	    // sanitize
	    $this->opretion_name  = $this->opretion_name;
	    $this->new_user_id  = $this->new_user_id;
	    $this->created_by  =  $this->current_user_id;
	    $created_date = date('Y-m-d H:i:s');

	    // bind values
	    $stmt->bindParam(":opretion_name", $this->opretion_name);
	    $stmt->bindParam(":new_user_id", $this->new_user_id);
	    $stmt->bindParam(":created_by", $this->created_by);
	    $stmt->bindParam(":created_date", $created_date);

	    // execute query
	    if($stmt->execute()){
	        return true;
	    }
	 
	    return false;
	     
	}


}