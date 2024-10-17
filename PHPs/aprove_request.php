<?php  

	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
 
 	$uid    = $_GET['uid'];
 	$friend = $_GET['friend'];
 	$type   = $_GET['type'];


 
 

	// accept
 	if($type=='accept'){
		$qry = "INSERT into relation (uid, friend) VALUE ('$uid', '$friend')";

		$delete_request = "DELETE from friend_request 
 			where (user = '$uid' and friend = '$friend')
 			or (user = '$friend' and friend = '$uid')";

		$conn->query($delete_request); 
	}
 	if($type=='remove')
 		$qry = "DELETE from friend_request 
 			where (user = '$uid' and friend = '$friend')
 			or (user = '$friend' and friend = '$uid')";

 	/*DELETE FRIEND !*/
/* 	if($type="delete")
 		"DELETE from friend_request where "*/

	$res = $conn->query($qry); 
	if($res){
		$result = array(
			'state'=> 200,
			'message'=> $type,
		);
		echo json_encode($result);   
	}
?>