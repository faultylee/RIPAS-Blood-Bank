<?php

require_once("DB.php");
DB::setup(DB_NAME, DB_USER, DB_PASS, DB_HOST);
DB::debug();
$temp_data=json_decode(file_get_contents("blood_types.json"));

$status_level=array("N/A","very low","low","ok");

$blood_type=array();
foreach($temp_data as $b){
	$blood_type[$b[0]]=array("give"=>explode(",",$b[1]),"recieve"=>explode(",",$b[2]));
}

$bank_id=1;

if(@$_POST["action"]=="update_blood"){
	foreach($blood_type as $type=>$data){
		$status=@$_POST["status_".$type];
		if($status)
			DB::query("insert into bloods(bank_id,type,status) values(?,?,?)",array($bank_id,$type,$status));
	}
}


function get_latest($bank_id){
	$temp=DB::fetchAll("SELECT * FROM bloods where bank_id=? GROUP BY TYPE , bank_id ORDER BY TIME DESC",$bank_id);
	$return=array();
	foreach($temp as $blood){
		
	}
}





?>
<pre>
$_POST:
<?php print_r($_POST); ?>

DB data:
<?php print_r($latest); ?>
</pre>


<style>
	label {width:40px;display:block;float:left;}
</style>

<h1>Update Levels</h1>
<form method="post">
<input type="hidden" name="action" value="update_blood"/>
<?php foreach($blood_type as $type=>$data): ?>
<label><?=$type;?></label>
<select name="status_<?=$type;?>">
<?php foreach($status_level as $i=>$n): ?>
<option value="<?=$i;?>"><?=$n;?></option>
<?php endforeach; ?>
</select><br/>
<?php endforeach; ?>
<input type="submit"/>
</form>
<?php

?>
