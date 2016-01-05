<?php

use carbon\core\util\StringUtils;

// Initialize the app
require_once('../app/init.php');

// Get the session ID
if($sessionId == null)
    $sessionId = getSessionKey();

// Make sure the session ID is valid
if(StringUtils::equals($sessionId, '', true, true))
    returnError('Session error.');

/**
* Return an error.
*
* @param string $msg The message
* @return string
*/
function returnError($msg) {
    // Return the error as JSON
    returnJson(Array('error' => $msg));
}

/**
* Return an array with data as JSON.
*
* @param array $array The array to return as JSON.
*/
function returnJson($array) {
    // Encode the json
    $json = json_encode($array);

    // Set the page headers
    header('Content-Type: application/json');

    // Print the json
    die($json);
}