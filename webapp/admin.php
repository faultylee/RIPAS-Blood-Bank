<?php

require_once("DB.php");
DB::setup("blood","root","");
$blood_type=json_decode(file_get_contents("blood_types.json"));

function get_compatible($o){
	global $blood_type;
	foreach($blood_type as $b){
		if($b[0]==$o)
			break;
	}
	print_r($b);
}


print_r(get_compatible("A+"));


?>

