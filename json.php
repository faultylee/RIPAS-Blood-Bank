<?php
if(isset($_GET["callback"])){
 header('Content-Type: application/javascript; charset=utf-8');
} else {
 header('Content-Type: application/json; charset=utf-8');
}
header('Cache-Control: max-age=420');
date_default_timezone_set('Asia/Brunei');


$cachefile=__DIR__ ."/cash/".(int)(date_timestamp_get(date_create())/7200).".json";
$headers = apache_request_headers(); 
if(file_exists($cachefile)){
if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($cachefile))) {
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($cachefile)).' GMT', true, 304);
}
	if(isset($_GET["callback"])) echo $_GET["callback"],"(";
	echo file_get_contents($cachefile);
	if(isset($_GET["callback"])) echo ")";
	exit();
}


function get_web_page( $url ) { //stole off stackoverflow lolz.
        $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

        $ch      = curl_init( $url );
        curl_setopt_array( $ch,array(
            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            //CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            //CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        ));
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );
        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return empty($errmsg) ?  mb_convert_encoding(str_replace(array("\r\n","\n","\r"),'',preg_replace('/\s+/', ' ',$content)),"HTML-ENTITIES","UTF-8") : array("error"=>":(");
    }


function get_blood(){
	$data=json_decode(get_web_page("https://www.facebook.com/feeds/page.php?format=json&id=116860361751905"),true);
	//$data=json_decode(get_web_page("http://localhost/freedom/feeds.js"),true);
	$movies=array();
	foreach($data["entries"] as $m){
		$content=strtoupper(preg_replace(array('/<br \/>/','/<.+?>/'),array("\n",''),$m["content"])); //*/
		$content=explode("\n",$content);
		$content=array_filter($content,function($v){
			return trim($v) && !preg_match("/\(.+?\).+?\(.+?\)/",$v);
		});
		$ret=array();
		foreach($content as $c){
			if(preg_match("/GRP.+?(AB|A|B|O).+(GOOD|LOW|VERY LOW|OK)/",$c,$matches)){
				$ret[]=array("type"=>$matches[1],"status"=>$matches[2]);
			}
		}
		if(count($ret))
			break;
	}
	return $ret;
}


$json_data= json_encode(get_blood()); 
file_put_contents($cachefile,$json_data);
if(isset($_GET["callback"])) echo $_GET["callback"],"(";
echo $json_data;
if(isset($_GET["callback"])) echo ")";


?>
