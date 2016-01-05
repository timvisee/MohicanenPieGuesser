<?php

use app\guess\Guess;
use app\guess\GuessManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>
    <div data-role="page" id="page-my-guesses">
        <?php
        // Construct the builder
        PageHeaderBuilder::create("Mijn schattingen")->setBackButton('index.php')->build();
        ?>
        <div data-role="main" class="ui-content">

            <p>Hier onder zie je een overzicht van de schattingen die je hebt ingestuurd via dit apparaat.</p>
            <br />

            <?php if(GuessManager::hasClientGuesses()): ?>
                <table data-role="table" data-mode="reflow" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-btn-theme="a" >
                    <thead>
                    <tr class="ui-bar-d">
                        <th>Naam</th>
                        <th>E-mail</th>
                        <th>Schatting</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get all personal guesses
                        $guesses = GuessManager::getClientGuesses();

                        foreach($guesses as $guess) {
                            // Make sure the instance type is valid
                            if(!($guess instanceof Guess))
                                continue;

                            // Print the guess in the table
                            echo '<tr><td>' . $guess->getFirstName() . ' ' . $guess->getLastName() . '</td>';
                            echo '<td>' . $guess->getMail() . '</td>';
                            echo '<td>' . $guess->getWeight() . ' gram</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p><i>Je hebt nog geen schattingen ingestuurd.</i></p>
            <?php endif;

            // Check whether this client has any guesses left, if so, show the entry button
            if(GuessManager::hasClientGuessesLeft()) {
                ?>
                <br />
                <fieldset data-role="controlgroup" data-type="vertical" class="ui-shadow ui-corner-all">
                    <?php if(!GuessManager::hasClientGuesses()): ?>
                        <a href="guess.php" class="ui-btn ui-icon-plus ui-btn-icon-left">Schatting insturen</a>
                    <?php elseif(GuessManager::hasClientGuessesLeft()): ?>
                        <a href="guess.php" class="ui-btn ui-icon-plus ui-btn-icon-left">Schatting voor een ander insturen</a>
                    <?php endif; ?>
                </fieldset>
                <?php
            }
            ?>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
<?php

// Include the page bottom
require_once('bottom.php');
