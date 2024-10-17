<?php  


	
	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	  
	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
 
 	$id         = $conn->real_escape_string( $_GET['id'] ); 
 	$username   = $conn->real_escape_string( $_GET['username'] ); 
 	$password   = $conn->real_escape_string( $_GET['password'] );  
  
	$qry = "UPDATE user set username='$username', password='$password' where id = '$id'";
	$res = $conn->query($qry);   

	if($res){
		echo 200;
	}else
		echo 0;

?>