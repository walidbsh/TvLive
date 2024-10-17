<?php  

	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
 
 	$uid    = $conn->real_escape_string ($_GET['uid']);
 	$friend = $conn->real_escape_string ($_GET['friend']);

 	$check = "SELECT user, friend 
 			  from friend_request 
 			  where user = '$uid' and friend = '$friend'";
 	 
 	$res = $conn->query($check); 
 	if(mysqli_num_rows($res) > 0){
 		echo "request existe";
 		exit();
 	}

	$qry = "INSERT into friend_request (user, friend) VALUE ('$uid', '$friend')";

	$res = $conn->query($qry);
	if($qry){
		echo 200;
	}else
		echo 404;

?>