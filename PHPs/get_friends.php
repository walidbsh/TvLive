<?php  

	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
 
 	$uid = $conn->real_escape_string($_GET['uid']);

 	if( isset( $_GET['me']) ) {
 		$qry = "SELECT * from user where id = '$uid'";
 		$res = $conn-> query($qry);
 		echo json_encode( $res->fetch_assoc() );
 		exit();
 	}else{
		$qry = "SELECT user.id, username, avatar FROM user INNER JOIN relation ON user.id = relation.friend where relation.uid = '$uid'";

		$qry_r = "SELECT user.id, username, avatar FROM user INNER JOIN relation ON user.id = relation.uid where relation.friend = '$uid'";


		$res = $conn->query($qry);   
		
		 
		$ress = array();
		while ($row = $res->fetch_assoc())
			$ress[] = $row;

		$res = $conn->query($qry_r); 
		while ($row = $res->fetch_assoc())
			$ress[] = $row;
 	}
 	echo json_encode($ress);

?>