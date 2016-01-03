<?php

// Include the pusher library
require_once('../../lib/pusher/Pusher.php');

// Create a pusher instance
$pusher = new Pusher('1ae3f01040df0206bf68', 'e885f550fba2b1e0d34f', '164045');

// Authenticate the request
echo $pusher->socket_auth($_POST['channel_name'], $_POST['socket_id']);
