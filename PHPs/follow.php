<?php  

	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
 
 	$follower = $conn->real_escape_string ($_GET['follower']);
 	$followed = $conn->real_escape_string ($_GET['followed']);

 	$check = "SELECT user, friend 
 			  from friend_request 
 			  where user = '$uid' and friend = '$friend'";
 	 
 	$res = $conn->query($check); 
 	if(mysqli_num_rows($res) > 0){
 		echo "request existe";
 		exit();
 	}

	$qry = "INSERT into followers (follower_id, followed_id) VALUE ('$follower', '$followed')";

	$res = $conn->query($qry);
	if($qry){
		$result = array(
			"status"=>200,
			"message"=> "followed"
		);
	}else
		$result = array(
			"status"=>404,
			"message"=> "failed"
		);

?>