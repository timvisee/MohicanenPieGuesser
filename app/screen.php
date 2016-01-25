<?php

use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>
    <div data-role="page" id="page-screen" data-theme="b">
        <?php
        // Construct the builder
        PageHeaderBuilder::create('Mohicanen NJO&nbsp;&nbsp;&middot;&nbsp;&nbsp;Hoe zwaar is de taart?')->build(true);
        ?>
        <div data-role="main" class="ui-content">

            <br />

            <fieldset class="ui-grid-a">
                <div class="ui-block-a" style="width: 60%;">
                    <div data-role="main" class="ui-content" align="center">
                        <img src="<?=$site_root; ?>style/image/cake/cake.png" style="height: 210px;" />
                    </div>
                </div>
                <div class="ui-block-b" style="width: 40%;">
                    <div data-role="main" class="ui-content" align="center">
                        <img src="<?=$site_root; ?>style/image/logo/logo_original.png" style="height: 210px;" />
                    </div>
                </div>
            </fieldset>

            <br />
            <br />
            <br />

            <fieldset class="ui-grid-a">
                <div class="ui-block-a" style="width: 60%; padding-right: 20px; padding-top: 10px;">
                    <div id="guess-chart" style="height: 100%;"></div>
                </div>
                <div class="ui-block-b" style="width: 40%; padding-left: 20px;">
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
                </div>
            </fieldset>

            <br />
            <br />

            <center>
                <div id="guess-counter-container">
                    Totaal aantal schattingen: <div id="guess-counter">?</div>
                </div>
            </center>
        </div>

        <?php PageFooterBuilder::create()->build(true); ?>
    </div>
<?php

// Include the page bottom
require_once('bottom.php');
