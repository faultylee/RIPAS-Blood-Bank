<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mohdlee
 */

//Facebook SDK taken from https://github.com/facebook/facebook-php-sdk
require( './facebook/facebook.php' );
require( '../config.php' );


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

?>

<html>
<head></head>
<body>

<?php
if($user_id) {

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

		//Retreive Pages administered by this user
		//https://graph.facebook.com/me/accounts?access_token=
		$user_pages = $facebook->api('/me/accounts', 'GET');
		//echo "<pre>" . print_r($user_pages, true) . "</pre>";

		if (null != $user_pages && array_key_exists('data', $user_pages)) {
			foreach($user_pages['data'] as $user_page_detail) {
				?>
				<ul>
					<li>Page Name: <?php echo $user_page_detail['name'] ?></li>
					<li>id: <?php echo $user_page_detail['id'] ?></li>
					<li>access_token: <?php echo $user_page_detail['access_token'] ?></li>
					<li>Click <a href="oauth_facebook_page.php?code=<?php echo $user_page_detail['access_token'] ?>&page_id=<?php echo $user_page_detail['id'] ?>">here</a> to gain access to this page</li>
				</ul>
				<?php
			}
		}
		else{
			echo "<p>You don't have any Facebook page associated with your Facebook account</p>";
		}


	} catch(FacebookApiException $e) {
		// If the user is logged out, you can have a
		// user ID even though the access token is invalid.
		// In this case, we'll get an exception, so we'll
		// just ask the user to login again here.
		show_login();
		error_log($e->getType());
		error_log($e->getMessage());
	}
} else {

	// No user, so print a link for the user to login
	// To post to a user's wall, we need publish_stream permission
	// We'll use the current URL as the redirect_uri, so we don't
	// need to specify it here.
	// offline_access = non-expiring access token
	// manage_pages = posting to page
	show_login();
}

function show_login() {
	global $facebook;
	$login_url = $facebook->getLoginUrl( array( 'scope' => 'offline_access,manage_pages' ) );
	echo 'Please <a href="' . $login_url . '">Login to facebook and retrieve access token</a>';
}

?>

</body>
</html>
