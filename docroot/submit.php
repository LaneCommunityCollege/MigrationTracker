<?php
require_once("config.php");

//Everything in this file requires authentication
session_start();
if(!isset($_SESSION['user'])){
	echo "not logged in";
	die();
}
session_regenerate_id();

/*********************************************
 * Responds to requests to delete info
 ********************************************/
if(isset($_GET['delete'])){
	$stmt = $db->prepare("DELETE FROM urls WHERE id=:id");
	$success = $stmt->execute(array('id' => (int)$_GET['delete']));
	echo $success;
	$stmt = $db->prepare("INSERT INTO history(linkid, time, action, author) VALUES(:linkid, :time, :action, :author)");
	$success = $stmt->execute(array('linkid' => (int)$_GET['delete'], 'time' => date('Y-m-d H:i:s'), 'action' => 'Deleted', 'author'=>$_SESSION['user'])); 
}

//make sure we're not missing one of the chunks of the form
elseif (!isset($_POST['oldurl']) || !isset($_POST['newurl']) || !isset($_POST['chunk']) || !isset($_POST['status'])){
	echo "missing post variable";
}
//Check to see if there's any invalid urls:
elseif(substr(preg_replace($valid_urls, '', $_POST['newurl']),0,1) != '/'){
    echo "Invalid New Url - must start with a /";
}
elseif(substr(preg_replace($valid_urls, '', $_POST['oldurl']),0,1) != '/'){
    echo "Invalid Old Url - must start with a /";
}
//Check if there's an invalid status
elseif (!in_array($_POST['status'], array('stub','review','complete','checked-out','archive-todo','archived','intranet-todo','intraneted'))){
	echo 'Not a valid Status';
}
//TODO chunk validation
else{
    //probably silly to be doing this here:
    $_POST['newurl'] = preg_replace($valid_urls, '', $_POST['newurl']);
    $_POST['oldurl'] = preg_replace($valid_urls, '', $_POST['oldurl']);
	//regardless of what happens, we're going to need to know if it already exists
	$stmt = $db->prepare("SELECT * FROM urls WHERE oldurl=:oldurl");
	$stmt->execute(array('oldurl' => $_POST['oldurl']));
	$exists = $stmt->rowCount();
	
	/*********************************************
	 * Responds to requests to edit an entry
	 ********************************************/
	if($_POST['type'] == 'edit'){
		if(!$exists){
			echo "Old URL not found in DB";
		}
		else{
			//get the id of the one we're going to update here:
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$id = $result['id'];
			$stmt = $db->prepare("UPDATE urls SET nodenumber=:nodenumber, oldurl=:oldurl, newurl=:newurl, chunk=:chunk, status=:status WHERE id=:id");
			$success = $stmt->execute(array('nodenumber' => $_POST['nodenumber'], 'oldurl' => $_POST['oldurl'], 'newurl' => $_POST['newurl'], 'chunk' => $_POST['chunk'], 'status' => $_POST['status'], 'id'=>$id));
			echo $success;
			$stmt = $db->prepare("INSERT INTO history(linkid, time, action, author) VALUES(:linkid, :time, :action, :author)");
			//TODO it'd be cool if we could report on what had been changed
			$success = $stmt->execute(array('linkid' => $id, 'time' => date('Y-m-d H:i:s'), 'action' => 'Modified', 'author'=>$_SESSION['user'])); 	
		}
	}

	/*********************************************
	 * Responds to requests to add an entry
	 ********************************************/
	else{
		//elif new one, test if it exists in the db already
		if($exists){
			echo "Already Exists!";
		}
		else{
			$stmt = $db->prepare("INSERT INTO urls(nodenumber,oldurl,newurl,chunk,status) VALUES (:nodenumber, :oldurl, :newurl, :chunk, :status)");
			$success = $stmt->execute(array('nodenumber' => $_POST['nodenumber'], 'oldurl' => $_POST['oldurl'], 'newurl' => $_POST['newurl'], 'chunk' => $_POST['chunk'], 'status' => $_POST['status']));
			$id = $db->lastInsertID();
			echo $success;
			$stmt = $db->prepare("INSERT INTO history(linkid, time, action, author) VALUES(:linkid, :time, :action, :author)");
			$success = $stmt->execute(array('linkid' => $id, 'time' => date('Y-m-d H:i:s'), 'action' => 'Created', 'author'=>$_SESSION['user'])); 
		}
	}
}
?>
