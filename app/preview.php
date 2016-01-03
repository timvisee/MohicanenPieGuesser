<?php

use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>
    <div data-role="page" id="page-preview">
        <?php
        // Construct the builder
        $builder = PageHeaderBuilder::create();

        // Check whether to add a back button
        if(isset($_GET['back']))
            $builder->setBackButton('index.php');

        // Build the header
        $builder->build();
        ?>
        <div data-role="main" class="ui-content">

            <center>
                <div id="guess-counter-container">
                    Schattingen: <div id="guess-counter">0</div>
                </div>
            </center>

            <br />

            <div id="guess-graph" style="height: 250px;"></div>

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
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
<?php

// Include the page bottom
require_once('bottom.php');
