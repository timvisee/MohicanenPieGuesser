<?php

use app\config\Config;

// Initialize the app
require_once('../../app/init.php');

// Include the pusher library
require_once('../../lib/pusher/Pusher.php');

// Gather the pusher key, secret and application ID
$authKey = Config::getValue('pusher', 'auth_key', '');
$secret = Config::getValue('pusher', 'secret', '');
$appId = Config::getValue('pusher', 'app_id', '0');

// Create a pusher instance with the proper key, secret and application ID
$pusher = new Pusher($authKey, $secret, $appId);

// Authenticate the request
echo $pusher->socket_auth($_POST['channel_name'], $_POST['socket_id']);
