<?php

use app\guess\GuessManager;
use app\util\AccountUtils;

// Initialize the ajax scripts
require_once('ajaxinit.php');

// Make sure all parameters are set
if(!isset($_GET['guess_first_name']) || !isset($_GET['guess_last_name']) || !isset($_GET['guess_mail']) || !isset($_GET['guess_weight']))
    returnError("Received invalid data. Some parameters are missing.");

// Get all parameters
$firstName = $_GET['guess_first_name'];
$lastName = $_GET['guess_last_name'];
$mail = $_GET['guess_mail'];
$weight = $_GET['guess_weight'];

// Make sure the full name is valid
if(!AccountUtils::isValidName($firstName) || !AccountUtils::isValidName($lastName))
    returnError("Ongeldige naam.");

// Make sure the mail is valid
if(!AccountUtils::isValidMail($mail))
    returnError("Ongeldig E-mail adres.");

// TODO: Make sure the guessed value is valid!
if(!GuessManager::hasClientGuessesLeft())
    returnError("Maximum aantal schattingen overschreden.");

// Add the guess
$guess = GuessManager::createGuess($firstName, $lastName, $mail, $weight);

// Return the result with JSON
returnJson(array('result' => 'success'));
