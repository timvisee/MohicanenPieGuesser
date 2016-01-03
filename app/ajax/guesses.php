<?php

use app\guess\Guess;
use app\guess\GuessManager;

// Initialize the ajax scripts
require_once('ajaxinit.php');

// Get all guesses
$guesses = GuessManager::getGuesses();

// Create an array of guesses
$guessesJson = array();

// Add each guess to the JSON data array
foreach($guesses as $guess) {
    // Validate the guess instance
    if(!($guess instanceof Guess))
        returnError("Internal error occurred");

    // Append the guess
    $guessesJson[] = Array('firstName' => $guess->getFirstName(), 'weight' => $guess->getWeight());
}

// Return the result with JSON
returnJson($guessesJson);
