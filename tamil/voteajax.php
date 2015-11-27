<?php
	ob_start();
	session_start();
	$fromuser = $_GET['user'];
	$operation = $_GET['vote'];
	$id = $_GET['id'];
	$m = new MongoClient();
	$db = $m -> map;
	$collection = $db -> reports;
	$idArray = array('_id' => new MongoId($id));
	$cursor = $collection -> find($idArray);
	if(strcmp($operation, "up") == 0) {
		foreach ($cursor as $doc) {
			if(!in_array($fromuser, $doc["voters"])){
				$collection -> update($idArray, 
					array('$push' => array("voters" => $fromuser),
						'$inc' => array("votes" => 1)
					)
				);
			}
			else {
				$_SESSION['voteerror'] = 1;
				break;
			}
		}
	}
	else {
		foreach ($cursor as $doc) {
			if(in_array($fromuser, $doc["voters"])){
				$collection -> update($idArray, 
					array('$pull' => array("voters" => $fromuser),
						'$inc' => array("votes" => -1)
					)
				);
			}
			else {
				$_SESSION['voteerror'] = -1;
				break;
			}
		}
	}
	header('Location:report.php');
?>
