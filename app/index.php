<?php

use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>
<div data-role="page" id="page-login">
    <?php PageHeaderBuilder::create()->build(); ?>
    <div data-role="main" class="ui-content">

        <fieldset data-role="controlgroup" data-type="vertical" class="ui-shadow ui-corner-all">
            <a href="guess.php" class="ui-btn ui-icon-user ui-btn-icon-left">Gewicht raden</a>
        </fieldset>
    </div>
    <?php

    // Build the footer and sidebar
    PageFooterBuilder::create()->build();
    ?>
</div>
<?php

// Include the page bottom
require_once('bottom.php');
