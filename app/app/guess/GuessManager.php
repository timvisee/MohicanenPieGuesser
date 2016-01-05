<?php

namespace app\guess;

use app\config\Config;
use app\database\Database;
use app\registry\Registry;
use app\util\AccountUtils;
use carbon\core\datetime\DateTime;
use carbon\core\util\IpUtils;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class GuessManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'guess';

    /** Registry key used to define the maximum number of requests a client can make. */
    const REG_CLIENT_MAX_ENTRIES = 'client.maxEntries';

    /**
     * Get the database table name of the guesses.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Parse a guess instance.
     * Alias of Guess::parse();
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
        return Guess::parse($guess, $default);
    }

    /**
     * Get a list of all guesses.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All guesses.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getGuesses() {
        // Build a query to select the guesses
        $query = 'SELECT guess_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of guesses
        $guesses = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $guesses[] = new Guess($data['guess_id']);

        // Return the list of guesses
        return $guesses;
    }

    /**
     * Get the number of guesses.
     *
     * @return int Number of guesses.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getGuessCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT guess_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any guess with the specified ID.
     *
     * @param int $id The ID of the guess to check for.
     *
     * @return bool True if any guess exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isGuessWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid guess ID.');

        // Prepare a query for the database to list guesses with this ID
        $statement = Database::getPDO()->prepare('SELECT guess_id FROM ' . static::getDatabaseTableName() . ' WHERE guess_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any guess found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Return all guesses with the specified session ID.
     *
     * @param string $sessionId The session ID of the guess to check for.
     *
     * @return array An array of guesses. An empty array will be returned if there are no guesses for this session ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getGuessesWithSessionId($sessionId) {
        // Make sure the ID isn't null
        if($sessionId === null)
            throw new Exception('Invalid session ID.');

        // Prepare a query for the database to list guesses with this session ID
        $statement = Database::getPDO()->prepare('SELECT guess_id FROM ' . static::getDatabaseTableName() . ' WHERE guess_session_id=:session_id');
        $statement->bindParam(':session_id', $sessionId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of guesses
        $guesses = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $guesses[] = new Guess($data['guess_id']);

        // Return the list of guesses
        return $guesses;
    }

    /**
     * Return the number of guesses with the specified session ID.
     *
     * @param string $sessionId The session ID of the guess to check for.
     *
     * @return int The number of guesses for this session ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getGuessesWithSessionIdCount($sessionId) {
        // Make sure the ID isn't null
        if($sessionId === null)
            throw new Exception('Invalid session ID.');

        // Prepare a query for the database to list guesses with this session ID
        $statement = Database::getPDO()->prepare('SELECT guess_id FROM ' . static::getDatabaseTableName() . ' WHERE guess_session_id=:session_id');
        $statement->bindParam(':session_id', $sessionId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the number of guesses for this session ID
        return $statement->rowCount();
    }

    /**
     * Get all guesses made by the current client.
     *
     * @return array Array of guesses.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getClientGuesses() {
        return GuessManager::getGuessesWithSessionId(getSessionKey());
    }

    /**
     * Check whether this client has made any guesses.
     *
     * @return bool True if any guesses are made.
     */
    public static function hasClientGuesses() {
        return GuessManager::getClientGuessCount() > 0;
    }

    /**
     * Get the number of guesses made by this client.
     *
     * @return int Number of guesses made.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getClientGuessCount() {
        return self::getGuessesWithSessionIdCount(getSessionKey());
    }

    /**
     * Return the maximum number of guesses a client may submit.
     *
     * @return int Maximum number of guesses.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getMaximumGuessesPerClient() {
        return (int) Registry::getValue('client.maxEntries')->getValue();
    }

    /**
     * Get the number of guesses left for this client.
     *
     * @return int Number of guesses left.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getClientGuessesLeft() {
        return max(GuessManager::getMaximumGuessesPerClient() - self::getClientGuessCount(), 0);
    }

    /**
     * Check whether this client has any guesses left.
     *
     * @return bool True if this client has any guesses left.
     */
    public static function hasClientGuessesLeft() {
        return self::getClientGuessesLeft() > 0;
    }

    /**
     * Check if there's any guess with the specified mail.
     *
     * @param int $mail The mail of the guess to check for.
     *
     * @return bool True if any guess exists with this mail.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isGuessWithMail($mail) {
        // Make sure the mail isn't null
        if($mail === null)
            throw new Exception('Invalid mail.');

        // Prepare a query for the database to list guesses with this mail
        $statement = Database::getPDO()->prepare('SELECT guess_id FROM ' . static::getDatabaseTableName() . ' WHERE guess_mail=:mail');
        $statement->bindParam(':mail', $mail, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any guess found with this mail
        return $statement->rowCount() > 0;
    }

    /**
     * Make a new guess.
     *
     * @param string $firstName The first name.
     * @param string $lastName The last name.
     * @param string $mail The mail.
     * @param number $weight The weight in kilograms.
     *
     * @return Guess The created guess as object.
     *
     * @throws Exception throws if an error occurred.
     */
    // TODO: Properly add full names with accents and such!
    public static function createGuess($firstName, $lastName, $mail, $weight) {
        // Make sure the name is valid
        if(!AccountUtils::isValidName($firstName) || !AccountUtils::isValidName($firstName))
            throw new Exception('The name is invalid.');

        // Make sure the mail is valid
        if(!AccountUtils::isValidMail($mail))
            throw new Exception('The mail is invalid.');

        // TODO: Validate the weight!

        // Get the session ID
        $sessionId = getSessionKey();

        // Determine the creation date time
        $dateTime = DateTime::now();

        // Get the guess IP
        $ip = IpUtils::getClientIp();

        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (guess_session_id, guess_first_name, guess_last_name, guess_mail, guess_weight, guess_datetime, guess_ip) ' .
            'VALUES (:session_id, :first_name, :last_name, :mail, :weight, :guess_datetime, :ip)');
        $statement->bindValue(':session_id', $sessionId, PDO::PARAM_STR);
        $statement->bindValue(':first_name', $firstName, PDO::PARAM_STR);
        $statement->bindValue(':last_name', $lastName, PDO::PARAM_STR);
        $statement->bindValue(':mail', $mail, PDO::PARAM_STR);
        $statement->bindValue(':weight', $weight, PDO::PARAM_STR);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':guess_datetime', $dateTime->toString(), PDO::PARAM_STR);
        $statement->bindValue(':ip', $ip, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Get and return the guess instance
        return new Guess(Database::getPDO()->lastInsertId());
    }
}