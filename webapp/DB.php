<?php
/* taken from mfirdaus.net */
class DB{
	private static $_db;
	private static $_DEBUG;
	private static function death($e,$p=null){
		if($p && self::$_DEBUG ) $p->debugDumpParams();
		die(self::$_DEBUG ? $e->getMessage() : ":("); //fix elegance later?
	}
	static function setup($dbname,$usr,$pass,$host='localhost'){
		try{
			self::$_db= new PDO("mysql:host=$host;dbname=$dbname",$usr,$pass);
			self::$_db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}catch (PDOException $e){
			self::death($e);		
	   }
	}
	static function debug(){
		self::$_DEBUG=1;
	}
	static function query($sql,$args=null){
	  try{
		  $p=self::$_db->prepare($sql);
		  if($args && !is_array($args))
		  	$args = array($args);  //if args is not array
		  $p->execute(!empty($args) ? $args : null); //avoid tenary?
		  return $p;
	   }catch (PDOException $e){
			self::death($e,$p);
	   }
	}

	static function fetchAll($sql,$args=null){
		return self::query($sql,$args)->fetchAll(PDO::FETCH_ASSOC);
	}
	static function fetch($sql,$args=null){
		return self::query($sql,$args)->fetch(PDO::FETCH_ASSOC);
	}
	static function fetchColumn($sql,$args=null){
		return self::query($sql,$args)->fetchColumn();
	}
	static function lastId(){
		return self::$_db->lastInsertId();
	}

	static function insert($table,$values){ //table name and associative array of values.
		if(!$table || !is_array($values) || empty($values))
			return ["error"=>"error parameters"];
		$keys= implode(',',array_keys($values));
		$place=implode(',',array_fill(0,count($values),'?'));
		$sql="insert into $table($keys) values($place)"; //madness
		self::query($sql,array_values($values));
		return ["id"=>self::lastId()];
	}
	static function update($table,$where,$values){ //limited to ands and =s?
		if(!$table || !is_array($values) || empty($values) || empty($where))
			return ["error"=>"error parameters"];
		if(!is_array($where))
			$where=["id"=>$where]; //not sure want to put
		$valuesKeys=array_reduce(array_keys($values),function($v,$w){

			if(substr($w,-1)==="="){
				$op=substr($w,-2,1); //not safe?
				$w=substr($w,0,-2);
				$w="$w = $w $op ?"; //why does this feel not safe
			} else {
				$w.="=?";
			}
			return $v . ($v?", ":"") .$w;
		});
		$whereKeys=implode('? and ',array_keys($where));
		echo $sql="update $table set $valuesKeys where $whereKeys=?","<br/>";
		return self::query($sql,array_merge(array_values($values),array_values($where)));
	}
};
?>