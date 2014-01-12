<?php
require_once("DB.php");
require_once("config.php");
DB::setup(DB_NAME, DB_USER, DB_PASS, DB_HOST);
DB::debug();
$temp_data=json_decode(file_get_contents("blood_types.json"));
$status_level=array("N/A","very low","low","ok");
$blood_type=array();
foreach($temp_data as $b){
	$blood_type[$b[0]]=array("give"=>explode(",",$b[1]),"recieve"=>explode(",",$b[2]));
}

function get_latest($bank_id){
	$temp=DB::fetchAll("select * from (SELECT * FROM bloods where bank_id=? order by time desc) b  GROUP BY TYPE , bank_id;",$bank_id);
	$return=array();
	foreach($temp as $blood){
		$return[$blood["type"]]=$blood;
	}
	return $return;
}
?>