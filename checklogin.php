<?php
	ob_start();
	session_start();
	$username = $_POST['username'];
	$password = md5($_POST['password']);
	$m = new MongoClient();
	$db = $m -> map;
	$collection = $db -> users;
	$constituency = "";
	$cursor = $collection -> find(array('username' => $username));
    foreach ($cursor as $doc) {
        $constituency = $doc["constituency"];
    }
	$creds = array('username' => $username, 'pass' => $password, 'active' => 1);
	$count = $collection -> count($creds);
	if ($count) {
		$_SESSION['username'] = $username;
		$_SESSION['constituency'] = $constituency;
		header('Location:report.php');
	}
	else {
		$_SESSION['login-error'] = 1;
		header('Location:login.php');
	}
?>