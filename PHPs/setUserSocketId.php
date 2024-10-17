<?php  


	
	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	  
	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
 
 	$sid = $conn->real_escape_string( $_GET['sid'] ); 
 	$uid = $conn->real_escape_string( $_GET['uid'] ); 

	$qry = "UPDATE user set lastSocketId='$sid' where id = '$uid'";
	$res = $conn->query($qry);   

	

?>