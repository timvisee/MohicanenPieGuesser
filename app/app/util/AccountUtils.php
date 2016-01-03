<?php

/**
 * AccountUtils.php
 * Utilities class for account related things.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright � Tim Visee 2013, All rights reserved.
 */

namespace app\util;

// Prevent direct requests to this set_file due to security reasons
defined('APP_INIT') or die('Access denied!');

/**
 * AccountUtils class.
 *
 * @package app\util
 * @author Tim Visee
 */
class AccountUtils {

    /**
     * Validate a mail.
     *
     * @param string $mail The mail to validate.
     *
     * @return bool True if the mail is valid, false otherwise.
     */
    public static function isValidMail($mail) {
        return (bool) filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate a full name.
     *
     * @param string $fullName The full name to validate.
     *
     * @return bool True if the full name is valid, false otherwise.
     */
    public static function isValidName($fullName) {
        // Make sure the name is a string
        if(!is_string($fullName))
            return false;

        // Trim the name
        $fullName = trim($fullName);

        // Make sure the name doesn't contain any numbers
        if(preg_match('/[0-9]+/', $fullName))
            return false;

        // Make sure the name is at least 3 chars long
        return strlen($fullName) >= 2 && strlen($fullName) <= 128;
    }
}