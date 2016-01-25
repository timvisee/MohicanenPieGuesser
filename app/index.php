<?php

use app\guess\GuessManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>
<div data-role="page" id="page-main">
    <?php PageHeaderBuilder::create()->build(); ?>
    <div data-role="main" class="ui-content">

        <div data-role="main" class="ui-content" align="center">
            <img src="<?=$site_root; ?>style/image/cake/cake.png" style="max-height: 220px; max-width: 100%;" />
        </div>

        <div data-role="main" class="ui-content" align="center">
            <p>
                <b>Noot: </b>Deze actie is inmiddels verlopen.<br />
                <br />
                Welkom bij de Mohicanen NJO van 2016.<br />
                <br />
                Weet jij hoe zwaar deze taart is?<br />
                Stuur jouw schatting in en maak kans op het winnen van deze taart.
            </p>
        </div>

        <br />

        <fieldset data-role="controlgroup" data-type="vertical" class="ui-shadow ui-corner-all">
            <?php if(!GuessManager::hasClientGuesses()): ?>
                <a href="guess.php" class="ui-btn ui-icon-plus ui-btn-icon-left">Schatting insturen</a>
            <?php elseif(GuessManager::hasClientGuessesLeft()): ?>
                <a href="guess.php" class="ui-btn ui-icon-plus ui-btn-icon-left">Schatting voor een ander insturen</a>
            <?php endif; ?>
            <a href="overview.php" class="ui-btn ui-icon-info ui-btn-icon-left">Overzicht bekijken</a>
            <?php if(GuessManager::hasClientGuesses()): ?>
                <a href="myguesses.php" class="ui-btn ui-icon-bullets ui-btn-icon-left">Mijn schattingen</a>
            <?php endif; ?>
        </fieldset>
    </div>

    <?php PageFooterBuilder::create()->build(); ?>
</div>
<?php

// Include the page bottom
require_once('bottom.php');
