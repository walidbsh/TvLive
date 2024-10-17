<?php  

	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	}


	$limit = 3;
	$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
 
 	if(isset($_GET['by'])){ // send rooms created by uid 
 		$uid = $_GET['by'];
		$qry = "SELECT * from room where creator = '$uid'";

		$res = $conn->query($qry);   
			
			 
		$ress = array();
		while ($row = $res->fetch_assoc())
			$ress[] = $row;

		echo json_encode($ress);
 	}else{
 		if(isset($_GET['link'])){ // get room by id 
 			$link = $_GET['link'];
			$qry = "SELECT * from room where link = '$link'";

			$res = $conn->query($qry);   
			

			if(mysqli_num_rows($res) == 0){ // room has been deleted
				echo "404";
			}else{
				$ress = array();
				while ($row = $res->fetch_assoc())
					$ress = $row;

				echo json_encode($ress);
			}
 		}else{
 			if(isset($_GET['uid'])){ // get rooms of my friends 
 				//get my friend 
 				$target_user = $_GET['uid'];
  

				$new_query = "
				    SELECT combined_results.link, combined_results.date, combined_results.name, combined_results.thumbnail, combined_results.creator, combined_results.avatar, combined_results.username,
				           CASE 
				               WHEN followers.follower_id IS NOT NULL THEN 1
				               ELSE 0 
				           END AS isFollowed
				    FROM (
				        SELECT room.link, room.date, room.name, room.thumbnail, room.creator, user.avatar, user.username 
				        FROM relation 
				        INNER JOIN room ON relation.friend = room.creator 
				        INNER JOIN user ON user.id = room.creator 
				        WHERE relation.uid = '$target_user'
				        
				        UNION
				        
				        SELECT room.link, room.date, room.name, room.thumbnail, room.creator, user.avatar, user.username 
				        FROM relation 
				        INNER JOIN room ON relation.uid = room.creator 
				        INNER JOIN user ON user.id = room.creator 
				        WHERE relation.friend = '$target_user'
				    ) AS combined_results
				    LEFT JOIN followers AS followers
				    ON combined_results.creator = followers.followed_id AND followers.follower_id = '$target_user'
				    ORDER BY isFollowed DESC, combined_results.date DESC
				    LIMIT $limit OFFSET $offset";

 				$arr = array();
 				$result = $conn->query($new_query); 
 			 

 				while($row = $result->fetch_assoc()){
 					$arr[] = $row;
 				}

 				echo json_encode($arr);

 			}else{
 				//$target
 			}
 		}
 	}



	 
 	//echo json_encode($ress);

?>