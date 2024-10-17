<?php  

	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
 
 	$key = '%'. $conn->real_escape_string( $_GET['key'] ).'%';


	$qry = "SELECT * from user where username like '$key'";

	 
	$res = $conn->query($qry);   
	$ress = array();
	while ($row = $res->fetch_assoc())
		$ress[] = $row;
 	
 	echo json_encode($ress);

?>