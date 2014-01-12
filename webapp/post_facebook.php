<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mohdlee
 */

//Facebook SDK taken from https://github.com/facebook/facebook-php-sdk
require './libs/facebook/facebook.php';

require('config.php');

//Sample below taken from https://developers.facebook.com/docs/php/howto/postwithgraphapi/
//App Config for https://www.facebook.com/BGM.Blood.Bank
$config = array(
	'appId' => FB_APP_ID,
	'secret' => FB_APP_SECRET,
	'fileUpload' => false, // optional
	'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
);

$facebook = new Facebook($config);
$user_id = $facebook->getUser();
$page_id = @$_GET['page_id'];
?>

<html>
<head></head>
<body>

<?php
if(!is_numeric($page_id)){
	?>
	Specify Facebook page ID
	<form name="form_message" method="GET" action="<?php echo $_SERVER['PHP_SELF'] ."?". $_SERVER['QUERY_STRING'] ?>">
		<label for="page_id">Message:</label>
		<input name="page_id"/>
		<input type="submit" value="post" />
	</form>
	<?php
}
if($user_id && is_numeric($page_id)) {

	// We have a user ID, so probably a logged in user.
	// If not, we'll get an exception, which we handle below.
	try {
		/*$ret_obj = $facebook->api('/me/feed', 'POST',
			array(
				'link' => 'www.example.com',
				'message' => 'Posting with the PHP SDK!'
			));
		echo '<pre>Post ID: ' . $ret_obj['id'] . '</pre>';
		*/
		$access_token = $facebook->getAccessToken();
		echo "<p>Access Token For RIPAS Blood Bank Page :";
		print_r($access_token);
		echo "</p>";
		// Give the user a logout link
		echo '<br /><a href="' . $facebook->getLogoutUrl() . '">Logout Facebook</a>';


		if (array_key_exists('new_message', $_POST)) {
			//Post the message

			$new_message = strip_tags($_POST['new_message']);
			date_default_timezone_set('Asia/Brunei');
			$new_message .= "\r\n\r\nPosted from Brunei Blood Bank Status App: " . date('d/m/Y h:i:s a', time());
			$user_page_post = $facebook->api("/$page_id/feed", 'POST',
				array("message" => $new_message));
			$new_message = htmlspecialchars($new_message);
			echo "<p>Posted message:$new_message</p>";
			echo "<pre>" . print_r($user_page_post, true) . "</pre>";
		}
		?>
		<form name="form_message" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ."?". $_SERVER['QUERY_STRING'] ?>">
			<label for="new_message">Message:</label>
			<textarea name="new_message"></textarea>
			<input type="submit" value="post" />
		</form>
		<?php

	} catch(FacebookApiException $e) {
		echo 'Please <a href=' . $_SERVER['PHP_SELF'] . "?". $_SERVER['QUERY_STRING'] . '>login again.</a>';
		error_log($e->getType());
		error_log($e->getMessage());
	}
} else {
	echo 'Please <a href=' . $_SERVER['PHP_SELF'] . "?". $_SERVER['QUERY_STRING'] . '>login again.</a>';

}

?>

</body>
</html>
