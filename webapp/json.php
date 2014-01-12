<?php
require_once("header.php");


$temp=get_latest(1); //
$return=array();
foreach($temp as $b){
	$return[$b["type"]]=$status_level[$b["status"]];
}

echo json_encode($return);

?>