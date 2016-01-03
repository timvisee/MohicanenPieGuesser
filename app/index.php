<?php

use app\guess\GuessManager;use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>
<div data-role="page" id="page-main">
    <?php PageHeaderBuilder::create()->build(); ?>
    <div data-role="main" class="ui-content">

        <div data-role="main" class="ui-content" align="center">
            <img src="style/image/logo/logo_original.png" style="height: 120px;" />
        </div>

        <br />

        <fieldset data-role="controlgroup" data-type="vertical" class="ui-shadow ui-corner-all">
            <?php if(!GuessManager::hasClientGuesses()): ?>
                <a href="guess.php" class="ui-btn ui-icon-plus ui-btn-icon-left">Schatting insturen</a>
            <?php elseif(GuessManager::hasClientGuessesLeft()): ?>
                <a href="guess.php" class="ui-btn ui-icon-plus ui-btn-icon-left">Schatting voor een ander insturen</a>
            <?php endif; ?>
            <a href="overview.php?back" class="ui-btn ui-icon-info ui-btn-icon-left">Overzicht bekijken</a>
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
