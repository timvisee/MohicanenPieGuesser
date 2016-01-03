<?php

use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>
<div data-role="page" id="page-main">
    <?php PageHeaderBuilder::create()->build(); ?>
    <div data-role="main" class="ui-content">

        <div data-role="main" class="ui-content" align="center">
            <img src="style/image/logo/logo_original.png" style="height: 120px;" />
            <br />
            <br />
        </div>

        <fieldset data-role="controlgroup" data-type="vertical" class="ui-shadow ui-corner-all">
            <a href="guess.php" class="ui-btn ui-icon-carat-r ui-btn-icon-left">Gewicht raden</a>
            <a href="preview.php?back" class="ui-btn ui-icon-info ui-btn-icon-left">Overzicht bekijken</a>
        </fieldset>
    </div>

    <?php PageFooterBuilder::create()->build(); ?>
</div>
<?php

// Include the page bottom
require_once('bottom.php');
