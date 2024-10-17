<?php  

	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');

	$conn = new mysqli("localhost","root","","test");

	// Check connection
	if ($conn -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $conn -> connect_error;
	  exit(); 
	} 
  
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
				    ORDER BY isFollowed DESC, combined_results.date DESC";

 				$arr = array();
 				$result = $conn->query($new_query); 
 			 

 				while($row = $result->fetch_assoc()){
 					$arr[] = $row;
 				}

 		 



	 
 	//echo json_encode($ress);

?>