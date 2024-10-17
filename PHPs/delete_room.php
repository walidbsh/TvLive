<?php  

	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	  
	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
 
 	$rid = $conn->real_escape_string( $_GET['rid'] );  
  
	$qry = "DELETE room where id = '$rid'";
	$res = $conn->query($qry);   

 

?>