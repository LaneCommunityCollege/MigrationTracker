<?php
require_once("config.php");

session_cache_limiter('nocache');
header('Expires: ' . gmdate('r', 0));
header('Content-type: application/json');

/**************************************************************** 
 * Responds to Links Information (linksTable)
 ***************************************************************/
if(isset($_GET['links'])){
	//figure out when the last update was made:
	$db2 = new PDO("mysql:host=localhost;dbname=information_schema", $dbuser, $dbpw);
	$stmt = $db2->prepare("SELECT UPDATE_TIME FROM TABLES WHERE TABLE_NAME=:tablename");
	$stmt->execute(array("tablename"=>"urls"));
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	//if the user's time is before the table's update time, send them the new links
	if($_GET['links'] < strtotime($result['UPDATE_TIME'])){
		$rows = array();
		foreach($db->query("SELECT urls.*, users.email, users.name, nh.time as t FROM (SELECT author, linkid, time FROM history WHERE time=(SELECT MAX(time) FROM history AS f WHERE f.linkid=history.linkid)) AS nh, users, urls WHERE nh.author=users.userid AND urls.id=nh.linkid ORDER BY nh.time DESC") as $r){
			$r['email'] = md5(strtolower(trim($r['email'])));
			$rows[] = $r;
		}
		echo json_encode(array('updated'=>strtotime($result['UPDATE_TIME']),'rows'=>$rows));
	}
	else{
		echo json_encode(array('updated'=>'none'));
	}
}
/**************************************************************** 
 * Responds to URL history information requests
 ***************************************************************/
elseif(isset($_GET['history'])){
	$rows = array();
	$stmt = $db->prepare("SELECT history.time, history.action, history.author, urls.oldurl, urls.newurl, users.name, users.email FROM history, urls, users WHERE history.linkid=:linkid AND history.linkid=urls.id AND history.author=users.userid ORDER BY history.time DESC");
	$stmt->execute(array('linkid' => $_GET['history']));
	$rows = array();
	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		$r['email'] = md5(strtolower(trim($r['email'])));
		$rows[] = $r;
	}
	echo json_encode($rows);
}
/**************************************************************** 
 * Responds to URL Information requests - used for editing an entry
 ***************************************************************/
elseif(isset($_GET['rowdata'])){
	$stmt = $db->prepare("SELECT * FROM urls WHERE id = :id");
	$stmt->execute(array('id' => $_GET['rowdata']));
	echo json_encode(array($stmt->fetch(PDO::FETCH_OBJ)));
}
/**************************************************************** 
 * Responds to requests for a unique list of chunks
 ***************************************************************/
elseif(isset($_GET['chunks'])){
	$rows = array();
	foreach($db->query("SELECT DISTINCT chunk FROM urls") as $r){
		$rows[] = $r['chunk'];
	}
	echo json_encode($rows);
}
?>
