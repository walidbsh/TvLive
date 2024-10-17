<?php  


	
	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	  
	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
 
 	$uid  = $conn->real_escape_string( $_GET['uid']  ); 

 
	$qry = "SELECT friend, user.* FROM friend_request INNER JOIN user ON user.id = friend_request.user where friend_request.friend = '$uid'";


	 

	$res = $conn->query($qry);   
	$ress = array();
	while ($row = $res->fetch_assoc())
		$ress[] = $row;
 
 	echo json_encode($ress);

?>