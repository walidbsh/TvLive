<?php  
 
	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

/*	error_reporting(E_ALL & E_STRICT);
	ini_set('display_errors', '1');
	ini_set('log_errors', '0');
	ini_set('error_log', './');*/
	  
	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
  
 	$email    = $conn->real_escape_string( $_GET['email']); 
 	$password = $conn->real_escape_string( $_GET['password']); 



	$qry = "SELECT * from user where email = '$email'";
 
	$res = $conn->query($qry);  
	if(mysqli_num_rows( $res ) == 1){


		$fetched = $res->fetch_assoc();
		if( $fetched['password'] == $password ){
			$obj = array(
				"state"=>100,
				"id"=>$fetched['id'],
				"username"=>$fetched['username'],
				"avatar"=>$fetched['avatar']
			);
			echo json_encode($obj);

		}else{
			$obj = array( // user acount existe but password inccorect
				"state"=>404,
			);
			echo json_encode($obj);
		}


	}else{
		if(mysqli_num_rows( $res ) == 0){ // new user 
 
			$qry = "INSERT into user ( email, password ) values ('$email', '$password')";
		 
			if ($conn->query($qry) === TRUE) {
			    // Get the inserted ID
			    $last_id = $conn->insert_id;
			    $obj = array(
					"state"=>100,
					"id"=>$last_id
				);
			echo json_encode ($obj);
			} else {
			    echo "Error: " . $qry . "<br>" . $conn->error;
			}
		}
	} 


 

?>