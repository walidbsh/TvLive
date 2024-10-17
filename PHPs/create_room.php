<?php 

	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');


	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	}  

	$name    	       = $conn->real_escape_string( $_GET['name'] );
	$state   	       = $conn->real_escape_string( $_GET['state'] ); // public or private 
	$link      	       = $conn->real_escape_string( $_GET['room'] ); 
	$thumbnail 		   = $conn->real_escape_string( $_GET['thumbnail'] ); 
	$creator   	       = $conn->real_escape_string( $_GET['creator'] );

	//$name = preg_replace('/[^\w\s]/', '', $name);


	$qry = "insert into room (link, thumbnail, name, creator, public) values ('$link', '$thumbnail', '$name', '$creator', '$state')";

	$res = $conn->query($qry);
	
	if($res)
		echo `
				{
					state:'200'
					message:'image uploaded!'
				}
			`;
 

?>