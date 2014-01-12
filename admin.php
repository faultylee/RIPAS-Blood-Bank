<?php

require_once("config.php");
require_once("DB.php");
DB::setup(DB_NAME,DB_USER,DB_PASS,DB_HOST);
$blood_type=json_decode(file_get_contents("blood_types.json"));

function get_compatible($o){
	global $blood_type;
	foreach($blood_type as $b){
		if($b[0]==$o)
			break $b;
	}
}

print_r($blood_type);


?>

