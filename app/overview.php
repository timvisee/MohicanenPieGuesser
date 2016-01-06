<?php

use app\guess\GuessManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>
    <div data-role="page" id="page-preview">
        <?php PageHeaderBuilder::create('Overzicht')->setBackButton('index.php')->build(true); ?>
        <div data-role="main" class="ui-content">
            <p>Hier vindt je een live overzicht van alle ingestuurde schattingen.</p>
            <br />

            <center>
                <div id="guess-counter-container">
                    Totaal aantal schattingen: <div id="guess-counter">?</div>
                </div>
            </center>
            <br />

            <div id="guess-chart" style="height: 250px;"></div>
            <br />

            <p>Meest recente schattingen:</p>
            <br />

            <table data-role="table" id="guess-table" data-mode="reflow" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-btn-theme="a" >
                <thead>
                <tr class="ui-bar-d">
                    <th>#</th>
                    <th>Naam</th>
                    <th>Schatting</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
            <br />

            <fieldset data-role="controlgroup" data-type="vertical" class="ui-shadow ui-corner-all">
                <a href="myguesses.php" class="ui-btn ui-icon-bullets ui-btn-icon-left">Mijn schattingen</a>
            </fieldset>

            <?php if(GuessManager::hasClientGuessesLeft()): ?>
                <fieldset data-role="controlgroup" data-type="vertical" class="ui-shadow ui-corner-all">
                    <a href="guess.php" class="ui-btn ui-icon-plus ui-btn-icon-left">Schatting voor een ander insturen</a>
                </fieldset>
            <?php endif; ?>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
<?php

// Include the page bottom
require_once('bottom.php');
