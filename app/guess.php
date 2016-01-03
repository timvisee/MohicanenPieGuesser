<?php

use app\guess\GuessManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\util\AccountUtils;

// Include the page top
require_once('top.php');

// Get the register step
$guessStep = 1;
if(isset($_GET['guess_step'])) {
    // Get the value
    $registerStepValue = $_GET['guess_step'];

    // Make sure the value is an integer, or show an error page instead
    if(!is_numeric($registerStepValue))
        showErrorPage();

    // Set the register step
    $guessStep = (int) $registerStepValue;
}

// TODO: make sure the user can still guess

if($guessStep == 1):
    if(!GuessManager::hasClientGuesses() || isset($_GET['ignoreWarning'])):
        ?>
        <div data-role="page" id="page-guess" data-unload="false">
            <?php PageHeaderBuilder::create("Schatting insturen")->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    Vul hier onder uw voor- en achternaam in om mee te doen met het raden van het gewicht van de taart.<br /><br />
                    <i><?=__('general', 'note'); ?>: Om mee te kunnen doen moet je je echte naam invullen.</i>
                </p><br />

                <form method="GET" action="guess.php?guess_step=2">
                    <input type="text" name="guess_first_name" value="" placeholder="<?= __('account', 'firstName'); ?>" />
                    <input type="text" name="guess_last_name" value="" placeholder="<?= __('account', 'lastName'); ?>" />

                    <input type="submit" value="<?= __('navigation', 'continue'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
                </form>
            </div>

            <?php
            // Build the footer and sidebar
            PageFooterBuilder::create()->build();
            ?>
        </div>
        <?php

    else:
        ?>
        <div data-role="page" id="page-guess" data-unload="false">
            <?php PageHeaderBuilder::create("Schatting insturen")->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    Je hebt al eerder een schatting ingestuurd via dit apparaat.
                    Je mag maar &eacute;&eacute;n schatting per persoon insturen, anders worden je schattingen gediskwalificeerd.<br />
                    <br />
                    Klik op doorgaan als iemand die nog geen schatting ingestuurd heeft dit apparaat wilt gebruiken.
                </p><br />

                <fieldset data-role="controlgroup" data-type="vertical" class="ui-shadow ui-corner-all">
                    <a href="guess.php?ignoreWarning" class="ui-btn ui-icon-carat-r ui-btn-icon-left">Doorgaan</a>
                </fieldset>
            </div>

            <?php
            // Build the footer and sidebar
            PageFooterBuilder::create()->build();
            ?>
        </div>
        <?php
    endif;

elseif($guessStep == 2):

    // Get the name
    // TODO: Should we convert the entities here?
    $firstName = htmlentities(trim($_GET['guess_first_name']));
    $lastName = htmlentities(trim($_GET['guess_last_name']));

    // Make sure the name is valid
    if(!AccountUtils::isValidName($firstName) || !AccountUtils::isValidName($lastName))
        showErrorPage(__('register', 'invalidFullName'));

    ?>
    <div data-role="page" id="page-guess" data-unload="false">
        <?php PageHeaderBuilder::create("Schatting insturen")->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p><?=__('general', 'hello'); ?> <?=$_GET['guess_first_name']; ?>!</p><br />

            <p>Vul hier onder jouw E-mail adres in zodat we contact met je op kunnen nemen als je de gelukkige winnaar bent. Je E-mail adres is niet zichtbaar voor andere spelers.</p><br />

            <form method="GET" action="guess.php?guess_step=3">
                <input type="hidden" name="guess_first_name" value="<?=$firstName; ?>" />
                <input type="hidden" name="guess_last_name" value="<?=$lastName; ?>" />

                <input type="text" name="guess_mail" placeholder="<?= __('account', 'mail'); ?>" />

                <input type="submit" value="<?= __('navigation', 'continue'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
            </form>
        </div>

        <?php
        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        ?>
    </div>
    <?php

elseif($guessStep == 3):

    // Get the values
    $firstName = htmlentities(trim($_GET['guess_first_name']));
    $lastName = htmlentities(trim($_GET['guess_last_name']));
    $mail = htmlentities(trim($_GET['guess_mail']));

    // Make sure the mail is valid
    if(!AccountUtils::isValidMail($mail))
        showErrorPage(__('register', 'invalidMail'));

    if(GuessManager::isGuessWithMail($mail))
        showErrorPage(__('register', 'mailAlreadyInUse'));

    ?>
    <div data-role="page" id="page-guess" data-unload="false">
        <?php PageHeaderBuilder::create("Schatting insturen")->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>Vul hier onder naar uw schatting het gewicht van de taart in. Gebruik het tekstvakje om een precieze waarde in te vullen.<br /><br />
                <i><?=__('general', 'note'); ?>: Je kunt je schatting later niet meer aanpassen.</i></p><br />

            <form method="GET" action="guess.php?guess_step=4">
                <input type="hidden" name="guess_first_name" value="<?=$firstName; ?>" />
                <input type="hidden" name="guess_last_name" value="<?=$lastName; ?>" />
                <input type="hidden" name="guess_mail" value="<?=$mail; ?>" />

                <label for="guess-weight">Gewicht in gram:</label>
                <input name="guess_weight" id="guess-weight" value="<?=mt_rand(10, 4000); ?>" min="0" max="5000" step="0.1" data-highlight="true" type="range" data-popup-enabled="true">

                <br />

                <input type="submit" value="<?= __('navigation', 'continue'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
            </form>
        </div>

        <?php
        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        ?>
    </div>
    <?php

elseif($guessStep == 4):

    // Get the values
    $firstName = htmlentities(trim($_GET['guess_first_name']));
    $lastName = htmlentities(trim($_GET['guess_last_name']));
    $mail = htmlentities(trim($_GET['guess_mail']));
    $weight = htmlentities(trim($_GET['guess_weight']));

    // Make sure the mail is valid
    if(!AccountUtils::isValidMail($mail))
        showErrorPage(__('register', 'invalidMail'));

    if(GuessManager::isGuessWithMail($mail))
        showErrorPage(__('register', 'mailAlreadyInUse'));

    // TODO: Make sure the guessed weight is valid

    ?>
    <div data-role="page" id="page-guess-send" data-unload="false">
        <?php PageHeaderBuilder::create("Schatting insturen")->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <center>
                <table class="ui-responsive list-table">
                    <tr>
                        <td><?=__('account', 'firstName'); ?></td>
                        <td><?=$firstName; ?></td>
                    </tr>
                    <tr>
                        <td><?=__('account', 'lastName'); ?></td>
                        <td><?=$lastName; ?></td>
                    </tr>
                    <tr>
                        <td><?=__('account', 'mail'); ?></td>
                        <td><?=$mail; ?></td>
                    </tr>
                    <tr>
                        <td>Schatting</td>
                        <td><?=$weight; ?> gram</td>
                    </tr>
                </table>
            </center>
            <br />

            <p>Zijn de bovenstaande gegevens correct? Klik op insturen om je schatting in te sturen, of ga terug om uw gegevens aan te passen.</p><br />

            <form method="POST" action="guess.php?guess_step=5">
                <input type="hidden" name="guess_first_name" value="<?=$firstName; ?>" />
                <input type="hidden" name="guess_last_name" value="<?=$lastName; ?>" />
                <input type="hidden" name="guess_mail" value="<?=$mail; ?>" />
                <input type="hidden" name="guess_weight" value="<?=$weight; ?>" />

                <input type="submit" value="Schatting insturen" class="ui-btn ui-icon-check ui-btn-icon-right" />
            </form>
        </div>

        <?php
        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        ?>
    </div>
    <?php

elseif($guessStep == 5):

    // Get the values
    $firstName = htmlentities(trim($_POST['guess_first_name']));
    $lastName = htmlentities(trim($_POST['guess_last_name']));
    $mail = htmlentities(trim($_POST['guess_mail']));
    $weight = htmlentities(trim($_POST['guess_weight']));

    // Make sure the full name is valid
    if(!AccountUtils::isValidName($firstName) || !AccountUtils::isValidName($lastName))
        showErrorPage();

    // Make sure the mail is valid
    if(!AccountUtils::isValidMail($mail))
        showErrorPage();

    if(GuessManager::isGuessWithMail($mail))
        showErrorPage();

    // TODO: Make sure the guessed value is valid!

    // Create the user
    UserManager::createUser($username, $password, $mail, $firstName);

    ?>
    <div data-role="page" id="page-register" data-unload="false">
        <?php PageHeaderBuilder::create(__('account', 'register'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>
                <?=__('general', 'welcome'); ?> <?=$firstName; ?>!<br />
                <br />
                <?=__('register', 'registeredSuccessfullyVerifyMail'); // TODO: Show a note, that the mail address must be activated within a specific period! ?>
            </p><br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goToFrontPage'); ?></a>
            </fieldset>
        </div>

        <?php
        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        ?>
    </div>
    <?php

endif;

// Include the page bottom
require_once('bottom.php');