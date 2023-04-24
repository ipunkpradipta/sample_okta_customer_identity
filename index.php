<?php

// Import the Composer Autoloader to make the SDK classes accessible:
require 'vendor/autoload.php';

// Load our environment variables from the .env file:
(Dotenv\Dotenv::createImmutable(__DIR__))->load();

// Now instantiate the Auth0 class with our configuration:
$auth0 = new \Auth0\SDK\Auth0([
  'domain' => $_ENV['AUTH0_DOMAIN'],
  'clientId' => $_ENV['AUTH0_CLIENT_ID'],
  'clientSecret' => $_ENV['AUTH0_CLIENT_SECRET'],
  'cookieSecret' => $_ENV['AUTH0_COOKIE_SECRET']
]);

// Import our router library:
use Steampixel\Route;

// Define route constants:
// define('ROUTE_URL_INDEX', rtrim($_ENV['AUTH0_BASE_URL'], '/'));
// define('ROUTE_URL_LOGIN', ROUTE_URL_INDEX . '/login');
// define('ROUTE_URL_CALLBACK', ROUTE_URL_INDEX . '/callback');
// define('ROUTE_URL_LOGOUT', ROUTE_URL_INDEX . '/logout');


Route::add('/', function () use ($auth0) {
  $session = $auth0->getCredentials();

  if ($session === null) {
    // The user isn't logged in.
    echo '<p>Please <a href="/login">log in</a>.</p>';
    return;
  }
  // }else{
  //   // $auhtData = "AuthKey0";
  //   // $auhtData .= base64_encode(json_encode($session));
  //   // $auhtData .= "XX";
  //   $suffle1 = substr(str_shuffle('abcdef1234dbcfea765'), 0, 5);
  //   $suffle2 = substr(str_shuffle('78756ababefa66&@!@!&098123ababefabc4567abcde'), 0, 24);
  //   $concate = $suffle1 . json_encode($session) . $suffle2;
  //   $auhtData = "'" . base64_encode($concate) . "'"; 
  //   header("Location: http://localhost/ceisa_tpsonline/auth?authentication=" . $auhtData);
  //   exit;
  // }

  // The user is logged in.
  echo '<pre>';
  print_r($session);
  echo '</pre>';

  echo '<p>You can now <a href="/logout">log out</a>.</p>';
});

Route::add('/login', function () use ($auth0) {
  // It's a good idea to reset user sessions each time they go to login to avoid "invalid state" errors, should they hit network issues or other problems that interrupt a previous login process:
  $auth0->clear();

  // Finally, set up the local application session, and redirect the user to the Auth0 Universal Login Page to authenticate.
  header("Location: " . $auth0->login("http://localhost:3000/callback"));
  exit;
});

Route::add('/callback', function () use ($auth0) {
  // Have the SDK complete the authentication flow:
  $auth0->exchange("http://localhost:3000/callback");

  // Finally, redirect our end user back to the / index route, to display their user profile:
  header("Location: " . "http://localhost:3000");
  exit;
});

Route::add('/logout', function () use ($auth0) {
  // Clear the user's local session with our app, then redirect them to the Auth0 logout endpoint to clear their Auth0 session.
  header("Location: " . $auth0->logout("http://localhost/ceisa_tpsonline/auth"));
  exit;
});

// This tells our router that we've finished configuring our routes, and we're ready to begin routing incoming HTTP requests:
Route::run('/');