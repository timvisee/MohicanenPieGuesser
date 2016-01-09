<?php

use app\guess\GuessManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\template\PageSidebarBuilder;

// Include the page top
require_once('top.php');

?>
<div data-role="page" id="page-status">
    <?php PageHeaderBuilder::create(__('pageAbout', 'applicationStatus'))->setBackButton('index.php')->build(); ?>

    <div data-role="main" class="ui-content" align="center">
        <p><?=__('pageStatus', 'currentStatusShownBelow'); ?></p><br />

        <table class="ui-responsive list-table">
            <tr>
                <td><?=__('general', 'database'); ?></td>
                <td><span style="color: green;">Connected!</span></td>
            </tr>
            <tr>
                <td>Schattingen</td>
                <td><?=GuessManager::getGuessCount(); ?></td>
            </tr>
            <tr>
                <td>Sessie</td>
                <td><?=getSessionKey(); ?></td>
            </tr>
            <tr>
                <td><?=__('pageStatus', 'uptime'); ?></td>
                <td><?php
                    if(function_exists('sys_getloadavg')) {
                        try {
                            $display = '';
                            system("uptime", $display);
                            preg_match('/[^,]+,[^,]+/i', $display, $matches);
                            echo $matches[0];
                        } catch(Exception $e) {
                            echo '<i>Unknown</i>';
                        }
                    } else
                        echo '<i>Meer dan 15 dagen</i>';
                    ?></td>
            </tr>
            <tr>
                <td><?=__('pageStatus', 'cpuUsage'); ?></td>
                <td><?php
                    if(function_exists('sys_getloadavg')) {
                        // Get the CPU status
                        $cpu = sys_getloadavg();

                        echo $cpu[0] . ' (1 min avg)<br />';
                        echo $cpu[1] . ' (5 min avg)<br />';
                        echo $cpu[2] . ' (15 min avg)<br />';
                    } else
                        echo '<i>&lt;1%</i>';
                    ?></td>
            </tr>
        </table>
        <br />

        <p><i><?=__('pageStatus', 'pageRefreshesEveryTenSec'); ?>..</i></p>

        <script>
            var staticsRefreshTimer;
            $(document).on('pageshow', function() {
                if(staticsRefreshTimer == null)
                    staticsRefreshTimer = setInterval(function() {
                        if(getActivePageId() == 'page-status') {
                            showLoader('Refreshing page...');
                            refreshPage();
                            hideLoader();
                        }
                    }, 10000);
            });
        </script>
    </div>

    <?php PageFooterBuilder::create()->build(); ?>
</div>
<?php

// Include the page bottom
require_once('bottom.php');