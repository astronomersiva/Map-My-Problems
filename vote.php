<?php
	ob_start();
	session_start();
	if(isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$fromuser = $_GET['user'];
		$operation = $_GET['vote'];
		$id = $_GET['id'];
		$_SESSION["votedId"] = $id;
		if(strcmp($username, $fromuser) == 0) {

			$m = new MongoClient();
			$db = $m -> map;
			$collection = $db -> reports;
			$idArray = array('_id' => new MongoId($id));
			$cursor = $collection -> find($idArray);
			if(strcmp($operation, "up") == 0) {
				foreach ($cursor as $doc) {
					if ((!in_array($fromuser, $doc["voters"])) and (!in_array($fromuser, $doc["downvoters"]))){
						$collection -> update($idArray, 
							array('$push' => array("voters" => $fromuser),
								'$inc' => array("votes" => 1)
							)
						);
						$_SESSION["voteError"] = 0;
					}
					else if (in_array($fromuser, $doc["voters"])) {
						$_SESSION["voteError"] = 1;
					}
					else if (in_array($fromuser, $doc["downvoters"])) {
						$collection -> update($idArray, 
							array('$pull' => array("downvoters" => $fromuser)
							)
						);
						$collection -> update($idArray, 
							array('$inc' => array("votes" => 1)
							)
						);
						$_SESSION["voteError"] = 0;

					}
				}
			}

			else {
				foreach ($cursor as $doc) {

					if ((!in_array($fromuser, $doc["voters"])) and (!in_array($fromuser, $doc["downvoters"]))){
						$collection -> update($idArray, 
							array('$push' => array("downvoters" => $fromuser),
								'$inc' => array("votes" => -1)
							)
						);
						$_SESSION["voteError"] = 0;
					}

					else if (in_array($fromuser, $doc["downvoters"])) {
						$_SESSION["voteError"] = -1;
					}

					else if (in_array($fromuser, $doc["voters"])) {
						$collection -> update($idArray, 
							array('$pull' => array("voters" => $fromuser)
							)
						);
						$collection -> update($idArray, 
							array('$inc' => array("votes" => -1)
							)
						);
						$_SESSION["voteError"] = 0;
					}
					
				}
			}
		}
		header('Location:report.php');
	}
	else {
		header('Location:login.php');
	}
?>
