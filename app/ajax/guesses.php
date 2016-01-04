<?php

use app\guess\Guess;
use app\guess\GuessManager;

// Initialize the ajax scripts
require_once('ajaxinit.php');

// Get all guesses
$guesses = GuessManager::getGuesses();
$clientGuesses = GuessManager::getClientGuesses();

// Create an array of guesses
$guessesArray = array();
$clientGuessesArray = array();

// Add each guess to the data array
foreach($guesses as $guess) {
    // Validate the guess instance
    if(!($guess instanceof Guess))
        returnError("Internal error occurred");

    // Append the guess
    $guessesArray[] = array(
        'id' => $guess->getId(),
        'firstName' => $guess->getFirstName(),
        'weight' => $guess->getWeight()
    );
}

// Add each guess from this client to the data array
foreach($clientGuesses as $guess) {
    // Validate the guess instance
    if(!($guess instanceof Guess))
        returnError("Internal error occurred");

    // Append the guess
    $clientGuessesArray[] = array(
        'id' => $guess->getId(),
        'firstName' => $guess->getFirstName(),
        'lastName' => $guess->getLastName(),
        'weight' => $guess->getWeight()
    );
}

// Return the result with JSON
returnJson(
    array(
        'guesses' => $guessesArray,
        'clientGuesses' => $clientGuessesArray
    ));
