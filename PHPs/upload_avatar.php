<?php


	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');



	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 



	$targetDir = "avatar/";




	$targetFile = $targetDir . basename($_FILES["file"]["name"]);
	$id = $_POST['id'];
	// Check if the uploads directory exists, if not, create it
	if (!file_exists($targetDir)) {
	    mkdir($targetDir, 0777, true);
	}

	// Check if file was uploaded without errors
	if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
	    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
	        echo "The avatar ". htmlspecialchars(basename($_FILES["file"]["name"])) . " has been uploaded.";
	          
	        $avatar_url = "/". $targetFile;
			$qry = "UPDATE user set avatar='$avatar_url' where id = '$id'";
			$res = $conn->query($qry);   

	    } else {
	        echo "Sorry, there was an error uploading your avatar.";
	    }
	} else {
	    echo "Error: " . $_FILES["file"]["error"];
	}

?>