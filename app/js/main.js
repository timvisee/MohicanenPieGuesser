/**
 * Get the ID of the current active page.
 *
 * @returns string ID of active page.
 */
function getActivePageId() {
    return $.mobile.activePage.attr("id");
}

/**
 * Refresh the current jQuery mobile page.
 */
function refreshPage() {
    jQuery.mobile.changePage(window.location.href, {
        allowSamePageTransition: true,
        transition: 'none',
        reloadPage: true,
        reverse: false,
        changeHash: false
    });
}

/**
 * Check whether an element has an attribute.
 *
 * @param attrName The name of the attribute.
 *
 * @returns {boolean} True if the attribute exists, false otherwise.
 */
jQuery.fn.hasAttr = function(attrName) {
    // Get the attribute
    var attr = $(this[0]).attr(attrName);

    // Check if the attribute exists
    return (typeof attr !== typeof undefined && attr !== false);
};

$(document).on("click", ".show-page-loading-msg", function() {
    var $this = $( this ),
        msgText = $this.jqmData("msgtext") || $.mobile.loader.prototype.options.text;
    showLoader(msgText);
});

function showLoader(msgText) {
    $.mobile.loading("show", {
        text: msgText,
        textVisible: "true",
        theme: "b",
        textonly: false,
        html: ""
    });
}

function hideLoader() {
    $.mobile.loading("hide");
}



/**
 * Define the guesses refresh timeouts for a connected and disconnected network state.
 * @type {number}
 */
var GUESSES_REFRESH_TIMEOUT_CONNECTED = 1000 * 60 * 2;

/**
 * Define the guesses refresh timeouts for a connected and disconnected network state.
 * @type {number}
 */
var GUESSES_REFRESH_TIMEOUT_DISCONNECTED = 1000 * 5;



// Page dependend scripts
$(document).on("pageshow", function() {
    // Get the active page ID
    var pageId = getActivePageId();

    // Define whether a connection is available or not
    var connectionActive = false;

    // Set the start time of the script
    var startTime = Date.now();

    // Only run the following scripts on the specified pages
    if(pageId == 'page-guess-send' || pageId == 'page-preview' || pageId == 'page-screen') {
        // Create a new pusher instance
        var pusher = new Pusher('1ae3f01040df0206bf68', { authEndpoint: 'pusher/auth/auth.php' });

        // Get the connection state object and the popup
        var connectionIndicator = $('#connection-indicator');

        /**
         * Update the connection state.
         */
        function updateConnectionState() {
            // Get the state
            var state = pusher.connection.state;

            // Make sure a connection indicator is available
            if(connectionIndicator.length <= 0)
                return;

            // Define the new connection active state flag
            var newConnectionActive = false;

            // Get the current state
            var currentState = '';
            if(connectionIndicator.hasClass('connecting'))
                currentState = 'connecting';
            else if(connectionIndicator.hasClass('connected')) {
                currentState = 'connected';

                // Set the connection active state flag
                newConnectionActive = true;
            } else if(connectionIndicator.hasClass('unstable'))
                currentState = 'unstable';
            else if(connectionIndicator.hasClass('disconnected'))
                currentState = 'disconnected';
            else if(connectionIndicator.hasClass('unavailable'))
                currentState = 'unavailable';
            else if(connectionIndicator.hasClass('none'))
                currentState = 'none';

            // Determine the new state
            var newState = '';
            if(state == 'connecting')
                newState = 'connecting';
            else if(state == 'connected')
                newState = 'connected';
            else if(state == 'disconnected')
                newState = 'disconnected';
            else if(state == 'initialized')
                newState = 'none';
            else if(state == 'unavailable' || state == 'failed')
                newState = 'unavailable';

            // Change the indicator and show a popup if the state has changed
            if(currentState != newState) {
                // Remove all states
                connectionIndicator.removeClass('connecting');
                connectionIndicator.removeClass('connected');
                connectionIndicator.removeClass('unstable');
                connectionIndicator.removeClass('disconnected');
                connectionIndicator.removeClass('unavailable');
                connectionIndicator.removeClass('none');

                // Apply the new state to the DOM
                connectionIndicator.addClass(newState);

                // Determine whether to keep showing the state
                var keepShowingState = true;

                // Set the popup message
                var tooltipMessage = '';
                if(newState == 'connecting')
                    tooltipMessage = 'Verbinden...';
                else if(newState == 'connected') {
                    tooltipMessage = 'Verbonden';
                    keepShowingState = false;
                } else if(newState == 'unstable')
                    tooltipMessage = 'Verbinding instabiel';
                else if(newState == 'disconnected')
                    tooltipMessage = 'Verbinding verbroken';
                else if(newState == 'unavailable')
                    tooltipMessage = 'Geen verbinding mogelijk';
                else if(newState == 'none')
                    tooltipMessage = 'Geen verbinding';

                // Show the tooltip
                showConnectionIndicatorTooltip(tooltipMessage, !keepShowingState);
            }

            // Determine whether the connection active state flag has changed
            if(newConnectionActive != connectionActive) {
                // Update the connection active flag state
                connectionActive = newConnectionActive;

                // Check whether the connection was lost or whether it was connected again
                if(!connectionActive) {
                    // Stop the guess refresh timer
                    stopRefreshTimer();

                    // Set a tooltip above the chart
                    setTimeout(function() {
                        // Make sure there's still no connection available
                        if(connectionActive)
                            return true;

                        // Show a warning
                        showGuessChartTooltip("Schattingen verouderd", false);
                    }, 5000);

                } else {

                    // Do a late refresh after half a second if we're still connected
                    setTimeout(function() {
                        if(startTime + 2500 < Date.now())
                            refreshGuessesLate();
                    }, 500);

                    // Restart the refresh timer
                    startRefreshTimer();
                }
            }

            // Update the connection active flag state
            connectionActive = newConnectionActive;
        }

        // Connection indicator tooltip instance
        var connectionIndicatorTooltip = null;

        /**
         * Show a tooltip message next to the connection indicator.
         *
         * @param message The tooltip message to show.
         * @param autoHide [optional] True to automatically hide the tooltip again. Defaults to true.
         */
        function showConnectionIndicatorTooltip(message, autoHide) {
            // Create a tooltip for the indicator if it doesn't exist yet
            if(connectionIndicatorTooltip == null) {
                // You may use the 'multiple' option on the first tooltip as well, it will return an array of objects too
                var tooltips = connectionIndicator.tooltipster({
                    multiple: true,
                    delay: 0,
                    timer: 2500,
                    position: 'right',
                    offsetX: 4,
                    updateAnimation: true,
                    animation: 'fade'
                });

                // this is also a way to call API methods on the first tooltip
                connectionIndicatorTooltip = tooltips[0];
            }

            // Determine whether to automatically hide the tooltip
            var autoHideBool = true;
            if(typeof autoHide === 'undefined')
                autoHideBool = false;
            else
                autoHideBool = !!autoHide;

            // Update and show the tooltip
            connectionIndicatorTooltip
                .content(message)
                .option('autoClose', autoHideBool)
                .option('timer', autoHideBool ? 2500 : 0)
                .show();

            // Reposition the tooltip for half a second
            var positionFixInterval = setInterval(function() {
                connectionIndicatorTooltip.reposition();
            }, 5);
            setTimeout(function() {
                clearInterval(positionFixInterval);
            }, 1000);
        }

        // Guess chart tooltip instance
        var guessChartTooltip = null;

        /**
         * Show a tooltip message next to the guess chart.
         *
         * @param message The tooltip message to show.
         * @param autoHide [optional] True to automatically hide the tooltip again. Defaults to true.
         */
        function showGuessChartTooltip(message, autoHide) {
            // Create a tooltip for the indicator if it doesn't exist yet
            if(guessChartTooltip == null) {
                // You may use the 'multiple' option on the first tooltip as well, it will return an array of objects too
                var tooltips = $('#guess-chart').tooltipster({
                    multiple: true,
                    delay: 0,
                    timer: 2500,
                    position: 'top',
                    offsetX: 4,
                    updateAnimation: true,
                    animation: 'fade',
                    trigger: 'custom'
                });

                // this is also a way to call API methods on the first tooltip
                guessChartTooltip = tooltips[0];
            }

            // Determine whether to automatically hide the tooltip
            var autoHideBool = true;
            if(typeof autoHide === 'undefined')
                autoHideBool = false;
            else
                autoHideBool = !!autoHide;

            // Update and show the tooltip
            guessChartTooltip
                .content(message)
                .option('autoClose', autoHideBool)
                .option('timer', autoHideBool ? 2500 : 0)
                .show();

            // Reposition the tooltip for half a second
            var positionFixInterval = setInterval(function() {
                guessChartTooltip.reposition();
            }, 5);
            setTimeout(function() {
                clearInterval(positionFixInterval);
            }, 1000);
        }

        // Register all events for connections
        pusher.connection.bind('initialized', function() { updateConnectionState(); });
        pusher.connection.bind('connecting', function() { updateConnectionState(); });
        pusher.connection.bind('connected', function() { updateConnectionState(); });
        pusher.connection.bind('unavailable', function() { updateConnectionState(); });
        pusher.connection.bind('failed', function() { updateConnectionState(); });
        pusher.connection.bind('disconnected', function() { updateConnectionState(); });

        // Force update the connection state each second
        setInterval(function() { updateConnectionState(); }, 1000);

        // Update the state once on initialization
        updateConnectionState();

        // Subscribe to the guess updates channel
        var channel = pusher.subscribe('private-guessUpdates');

        // Wait for the subscription to succeed
        if(pageId == 'page-guess-send') {
            /**
             * Make a guess based on the current values.
             */
            function makeGuess() {
                // Show the loading indicator
                showLoader("Schatting insturen...");

                // Get the values
                var firstName = $("input[name=guess_first_name]").val();
                var lastName = $("input[name=guess_last_name]").val();
                var mail = $("input[name=guess_mail]").val();
                var weight = $("input[name=guess_weight]").val();

                // Make a request to make the guess
                var currentRequest = $.ajax({
                    type: "GET",
                    url: "ajax/makeguess.php",
                    data: {
                        guess_first_name: firstName,
                        guess_last_name: lastName,
                        guess_mail: mail,
                        guess_weight: weight
                    },
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    timeout: 10000,
                    success:function(data) {
                        // Show the error message if returned
                        if(data.hasOwnProperty('error')) {
                            alert("Error: " + data.error);
                            return;
                        }

                        // Show a status message
                        showLoader("Schatting verwerken...");

                        // Send the guess
                        sendGuess(firstName, lastName, weight);

                        // Continue to the overview page
                        setTimeout(function() {
                            // Continue
                            jQuery.mobile.navigate('guess.php?guess_step=6');

                            // Hide the loading indicator
                            hideLoader();
                        }, 150);
                    },
                    error: function(msg) {
                        // An error occurred, show a status message
                        if(msg.statusText)
                            alert("An error has been detected by Carbon CORE: " + msg.statusText);
                    },
                    complete: function() {
                        // Clear the current request variable
                        currentRequest = null;

                        // Hide the loading indicator
                        hideLoader();
                    }
                });
            }

            /**
             * Send a weight guess to other connected clients.
             *
             * @param firstName First name.
             * @param lastName Last name.
             * @param weight Weight.
             */
            function sendGuess(firstName, lastName, weight) {
                channel.trigger('client-newGuess', {
                    weight: weight,
                    firstName: firstName,
                    lastName: lastName});
            }

            // Execute the make guess method when the make guess button is pressed
            $('#make-guess-button').click(function() {
                makeGuess();
                return false;
            });
        }

        // Code for the preview page
        if(pageId == 'page-preview' || pageId == 'page-screen') {
            // Create an array of guesses and client guesses
            var guesses = [];
            var clientGuesses = [];
            var clientGuessesPlotted = [];

            // Set the step on the xaxis used in the graph and the minimum and maximum values
            var graphSteps = 100;
            var min = 0;
            var max = 5000;

            // Define the chart options
            var chartOptions = {
                chart: {
                    backgroundColor: '#F9F9F9', // 000547
                    renderTo: 'guess-chart',
                    type: 'areaspline'
                },
                title: {
                    text: ''
                },
                subtitle: { },
                xAxis: {
                    allowDecimals: false,
                    labels: {
                        formatter: function () {
                            return (this.value * graphSteps) + " gram";
                        },
                        style: { }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Aantal schattingen per gram',
                        style: {}
                    },
                    labels: {
                        style: {}
                    },
                    tickInterval: 1
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + Math.max(((this.x * graphSteps) - (graphSteps / 2)), min) + '</b> tot <b>' +
                            Math.min(((this.x * graphSteps) + (graphSteps / 2)), max) + '</b> gram<br /><b>' + this.y + '</b> schatting' +
                            (this.y != 1 ? 'en' : '');
                    },
                    crosshairs: [true]
                },
                plotOptions: {
                    areaspline: {
                        pointStart: 0,
                        marker: {
                            enabled: false,
                            symbol: 'circle',
                            radius: 2,
                            states: {
                                hover: {
                                    enabled: true
                                }
                            }
                        },
                        lineWidth: 4,
                        states: {
                            hover: {
                                lineWidth: 5
                            }
                        }
                    }
                },
                series: [{
                    name: 'Schattingen laden...',
                    data: [0]
                }],
                legend: {
                    enabled: false
                },
                colors: ['#0067B2'],
                credits: false
            };

            // Modify the chart to properly appear if shown on the big screen
            if(pageId == 'page-screen') {
                chartOptions.chart.backgroundColor = '#000459';
                chartOptions.xAxis.labels.style.fontSize = '19.2px';
                chartOptions.xAxis.labels.style.color = '#C1CFDA';
                chartOptions.xAxis.tickPixelInterval = 200;
                chartOptions.yAxis.labels.style.fontSize = '19.2px';
                chartOptions.yAxis.labels.style.color = '#C1CFDA';
                chartOptions.yAxis.title.style.fontSize = '19.2px';
                chartOptions.yAxis.title.style.color = '#C1CFDA';
                chartOptions.yAxis.gridLineColor = '#474D51';
                chartOptions.colors = ['#0085DD'];
            }

            // Create a new chart
            var chart = new Highcharts.Chart(chartOptions);

            /**
             * Get the number of guesses currently loaded.
             *
             * @returns {*}
             */
            function getGuessesCount() {
                return guesses.length;
            }

            /**
             * Update the table to put the 5 newest guesses in.
             */
            function updateTable() {
                var rowCount = 5;
                if(pageId == 'page-screen')
                    rowCount = 8;

                // Build the new table contents
                var html = '';
                for(var i = getGuessesCount() - 1; i >= Math.max(0, getGuessesCount() - rowCount); i--) {
                    html += "<tr>";
                    html += "<td>" + (i + 1) + "</td>\n";
                    html += "<td>" + guesses[i].firstName + "</td>";
                    html += "<td>" + guesses[i].weight + " gram</td>";
                    html += "</tr>";
                }

                // Set the table contents and update it
                $("#guess-table > tbody").html(html);
                $("#guess-table").table("refresh");
            }

            /**
             * Update the guess counter.
             */
            function updateGuessCounter() {
                $("#guess-counter").html(getGuessesCount());
            }

            /**
             * Update the chart with the newest data
             */
            function updateChart() {
                // Define the for loop index
                var i;

                // Get the chart options
                chartOptions = chart.options;

                // Generate the chart data
                var data = [];
                for(i = min; i <= max / graphSteps; i++)
                    data.push(0);
                for(i = 0; i < getGuessesCount(); i++)
                    data[Math.round(guesses[i].weight / graphSteps) - min]++;

                // Set the chart data
                chart.series[0].setData(data, true);

                // Plot the lines
                if(pageId != 'page-screen') {
                    // Loop through all the plotted client guesses, and remove the client guesses if they don't exist anymore
                    for(i = 0; i < clientGuessesPlotted.length; i++) {
                        // Get the ID
                        var plotId = clientGuessesPlotted[i];

                        // Make sure this ID is still in the client guesses list
                        var inList = false;
                        for(var j = 0; j < clientGuesses.length; j++) {
                            // Check whether the ID equals the current ID in the list
                            if(clientGuesses[j].id == plotId) {
                                // Set the flag
                                inList = true;

                                // Break the loop
                                break;
                            }
                        }

                        // Remove the plotted line if it doesn't exist anymore
                        if(!inList)
                            chart.xAxis[0].removePlotLine(plotId);
                    }

                    // Plot the client guesses
                    for(i = 0; i < clientGuesses.length; i++) {
                        // Get some parameters
                        var guessId = clientGuesses[i].id;

                        // Only plot the new line if it isn't plotted yet
                        if($.inArray(guessId, clientGuessesPlotted) == -1) {
                            var fullName = clientGuesses[i].firstName + ' ' + clientGuesses[i].lastName;

                            // Plot the guess line
                            chart.xAxis[0].addPlotLine({
                                color: '#000459',
                                dashStyle: 'solid',
                                value: Math.round(clientGuesses[i].weight / graphSteps) - min,
                                width: 2,
                                label: {
                                    text: fullName,
                                    align: 'right',
                                    rotation: 270,
                                    x: -7,
                                    y: +4,
                                    style: {
                                        color: '#000459'
                                    }
                                },
                                id: guessId
                            });

                            // Add the plot ID to the array
                            clientGuessesPlotted.push(guessId);
                        }
                    }
                }
            }

            /**
             * Update every preview.
             */
            function updateAll() {
                updateTable();
                updateGuessCounter();
                updateChart();
            }

            // The last state of the guesses refresh, true if succeed false otherwise
            var lastRefreshState = true;

            /**
             * Refresh the current guesses.
             *
             * @param showLoadingIndicator True to show a loading indicator.
             */
            function refreshGuesses(showLoadingIndicator) {
                // Show the indicator
                if(showLoadingIndicator)
                    showLoader("Schattingen laden...");

                // Make an AJAX request to load the station results
                var currentRequest = $.ajax({
                    type: "GET",
                    url: "ajax/guesses.php",
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    timeout: 10000,
                    success:function(data) {
                        // Set the last connection state
                        lastRefreshState = true;

                        // Show the error message if returned
                        if(data.hasOwnProperty('error')) {
                            alert("A fatal error has been detected by Carbon CORE: Failed to parse guesses data.");
                            return;
                        }

                        // The guesses and client guesses data
                        //noinspection JSUnresolvedVariable
                        guesses = data.guesses;
                        //noinspection JSUnresolvedVariable
                        clientGuesses = data.clientGuesses;

                        // Update everything
                        updateAll();
                    },
                    error: function() {
                        // Set the last connection state
                        lastRefreshState = false;

                        // An error occurred, show a status message
                        showGuessChartTooltip('Fout bij verversen', false);
                    },
                    complete: function() {
                        // Clear the current request variable
                        currentRequest = null;

                        // Hide the loading indicator
                        if(showLoadingIndicator)
                            hideLoader();

                        // Restart the guesses refresh timer
                        startRefreshTimer();
                    }
                });
            }

            // The guesses refresh timer
            var guessesRefreshTimer = null;

            /**
             * Start or restart the refresh timer.
             */
            function startRefreshTimer() {
                // Stop the current timer
                stopRefreshTimer();

                // Determine the refresh delay, based on the connection state
                var connectionTimeout = GUESSES_REFRESH_TIMEOUT_CONNECTED;

                // Use a connection timer of 5 seconds if the last connection failed
                if(!lastRefreshState)
                    connectionTimeout = GUESSES_REFRESH_TIMEOUT_DISCONNECTED;

                // Set up the timer
                guessesRefreshTimer = setInterval(function() {
                    refreshGuessesLate();
                }, connectionTimeout);
            }

            /**
             * Refresh the guesses after initialization.
             */
            function refreshGuessesLate() {
                // Make sure the script is running for at least three and a half seconds already
                if(startTime + 3500 > Date.now())
                    return true;

                // Show a status message
                showGuessChartTooltip('Verversen...', false);

                // Refresh the guesses data
                refreshGuesses(false);

                // Show a status message
                showGuessChartTooltip('Schattingen ververst', true);
            }

            /**
             * Stop the refresh timer.
             */
            function stopRefreshTimer() {
                // Clear the timer
                if(guessesRefreshTimer != null)
                    clearInterval(guessesRefreshTimer);

                // Reset the variable
                guessesRefreshTimer = null;
            }

            // Bind to the channel to process updates
            channel.bind('client-newGuess', function(data) {
                // Add the data to the list of guesses
                guesses.push({firstName: data.firstName, lastName: data.lastName, weight: data.weight});

                // Update everything
                updateAll();
            });

            // Refresh all the guesses
            refreshGuesses(true);

            // Start the refresh timer
            startRefreshTimer();
        }
    }
});