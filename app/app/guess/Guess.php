<?php

namespace app\guess;

use app\database\Database;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Guess {

    /** @var int The guess ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Guess ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Parse a guess instance.
     *
     * Valid instances:
     * - Guess instance.
     * - Guess ID as int.
     *
     * @param Guess|int $guess The guess instance, or the guess ID as int.
     * @param mixed|null $default [optional] The default value returned if the guess instance is invalid
     *
     * @return Guess|mixed The guess instance or the default value if the guess instance isn't valid.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function parse($guess, $default = null) {
        // Return the instance if it's already a Guess instance
        if($guess instanceof Guess)
            return $guess;

        // Make sure the instance is an integer, return the default value if not
        if(!is_int($guess) && !is_numeric($guess))
            return $default;

        // Parse the guess instance as int
        $guessId = (int) $guess;

        // Make sure an guess exists with this ID
        if(!GuessManager::isGuessWithId($guessId))
            throw new Exception('Unknown guess ID');

        // Construct and return the guess instance
        return new Guess($guessId);
    }

    /**
     * Get the guess ID.
     *
     * @return int The guess ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific guess.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list guesses with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . GuessManager::getDatabaseTableName() . ' WHERE guess_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        return $data[$columnName];
    }

    /**
     * Get the session ID.
     *
     * @return string Guess session ID.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getSessionId() {
        return $this->getDatabaseValue('guess_session_id');
    }

    /**
     * Get the first name.
     *
     * @return string Guess first name.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getFirstName() {
        return $this->getDatabaseValue('guess_first_name');
    }

    /**
     * Get the last name.
     *
     * @return string Guess last name.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getLastName() {
        return $this->getDatabaseValue('guess_last_name');
    }

    /**
     * Get the mail.
     *
     * @return string Guess mail.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getMail() {
        return $this->getDatabaseValue('guess_mail');
    }

    /**
     * Get the weight.
     *
     * @return number Guess weight.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getWeight() {
        return (float) $this->getDatabaseValue('guess_weight');
    }

    /**
     * Get the guess's creation date and time.
     *
     * @return DateTime Guess's creation date and time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getGuessDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('guess_datetime'));
    }

    /**
     * Get the IP.
     *
     * @return string Guess IP.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getIp() {
        return $this->getDatabaseValue('guess_ip');
    }
}
