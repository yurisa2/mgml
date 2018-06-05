<?php
session_start();
require 'include/ml/php-sdk/Meli/meli.php';
require 'include/ml/php-sdk/configApp.php';
require 'include/config.php';
$redirectURI = "https://localhost:8443/mgml/callback.php";

$meli = new Meli($appId, $secretKey);

if(isset($_GET['code']) || isset($_SESSION['access_token'])) {

	// If code exist and session is empty
	if(isset($_GET['code']) && !isset($_SESSION['access_token'])) {
		// //If the code was in get parameter we authorize
		try{
			$user = $meli->authorize($_GET["code"], $redirectURI);
      echo "foi pro auth";
			// Now we create the sessions with the authenticated user
			$_SESSION['access_token'] = $user['body']->access_token;
			$_SESSION['expires_in'] = time() + $user['body']->expires_in;
			$_SESSION['refresh_token'] = $user['body']->refresh_token;
      $tokens['access_token'] = $refresh['body']->access_token;
      $tokens['expires_in'] = time() + $refresh['body']->expires_in;
      $tokens['refresh_token'] = $refresh['body']->refresh_token;

      file_put_contents("tokens", $tokens);

			echo '<pre>';
			var_dump($user);
		}catch(Exception $e){
      echo "foi pro catch";

			echo "Exception: ",  $e->getMessage(), "\n";
		}
	} else {
		// We can check if the access token in invalid checking the time
		if($_SESSION['expires_in'] < time()) {
			try {
				// Make the refresh proccess
				$refresh = $meli->refreshAccessToken();

				// Now we create the sessions with the new parameters
				$_SESSION['access_token'] = $refresh['body']->access_token;
				$_SESSION['expires_in'] = time() + $refresh['body']->expires_in;
				$_SESSION['refresh_token'] = $refresh['body']->refresh_token;

				$tokens['access_token'] = $refresh['body']->access_token;
				$tokens['expires_in'] = time() + $refresh['body']->expires_in;
				$tokens['refresh_token'] = $refresh['body']->refresh_token;

        file_put_contents("tokens", $tokens);

			} catch (Exception $e) {
			  	echo "Exception: ",  $e->getMessage(), "\n";
			}
		}
	}

	echo '<pre>';
		print_r($_SESSION);
    $tokens['access_token'] = $refresh['body']->access_token;
    $tokens['expires_in'] = time() + $refresh['body']->expires_in;
    $tokens['refresh_token'] = $refresh['body']->refresh_token;

    file_put_contents("tokens", json_encode($tokens));

	echo '</pre>';

} else {
	echo '<a href="' . $meli->getAuthUrl($redirectURI, Meli::$AUTH_URL[$siteId]) . '">Login using MercadoLibre oAuth 2.0</a>';
}
