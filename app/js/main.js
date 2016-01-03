// The page refresh timer instance
var pageRefreshTimer = null;

/**
 * Start or restart the refresh timer.
 */
function startRefreshTimer() {
    // Stop the current timer
    stopRefreshTimer();

    // Set up the timer
    pageRefreshTimer = setInterval(function() {
        if(getActivePageId() != 'page-map') {
            showLoader('Refreshing page...');
            refreshPage();
            hideLoader();
        }
    }, 1000 * 60 * 2);
}

/**
 * Stop the refresh timer.
 */
function stopRefreshTimer() {
    // Clear the timer
    if(pageRefreshTimer != null)
        clearInterval(pageRefreshTimer);

    // Reset the variable
    pageRefreshTimer = null;
}

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






$(document).on("pageshow", function() {
    // Enable pusher logging - don't include this in production
    Pusher.log = function(message) {
        if (window.console && window.console.log) {
            window.console.log(message);
        }
    };

    // Get the active page ID
    var pageId = getActivePageId();

    if(pageId == 'page-guess-send' || pageId == 'page-preview') {
        // Create a new pusher instance
        var pusher = new Pusher('1ae3f01040df0206bf68', { authEndpoint: 'pusher/auth/auth.php' });

        // Subscribe to the guess updates channel
        var channel = pusher.subscribe('private-guessUpdates');

        // Wait for the subscription to succeed
        if(pageId == 'page-guess-send') {
            channel.bind('pusher:subscription_succeeded', function() {
                // Determine a random weight
                var weight = 12.5;
                var num = Math.random() * 12;
                for(var i = 0; i < num; i++)
                    weight += (Math.random() * 2) - 1;

                // Trigger a debug event
                channel.trigger('client-newGuess', {weight: weight, firstName: 'Timmeh', lastName: "Visse"});
            });
        }

        // Code for the preview page
        if(pageId == 'page-preview') {
            // Create an array of guesses
            var guesses = [];

            // Define the chart options
            var chartOptions = {
                chart: {
                    backgroundColor: '#F9F9F9',
                    renderTo: 'guess-graph',
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
                            return this.value + " KG"; // clean, unformatted number for year
                        },
                        tickInterval: 2
                    }
                },
                yAxis: {
                    title: {
                        text: 'Aantal schattingen per kilogram'
                    }
                },
                tooltip: {
                    pointFormat: '<b>{point.y:,.0f}</b> mensen geschat'
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
                        }
                    }
                },
                series: [{
                    name: 'Schattingen',
                    data: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                }],
                legend: {
                    enabled: false
                },
                credits: false
            };

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
                // Build the new table contents
                var html;
                for(var i = Math.max(0, getGuessesCount() - 5); i < getGuessesCount(); i++) {
                    html += "<tr>";
                    html += "<td>" + (i + 1) + "</td>\n";
                    html += "<td>" + guesses[i].firstName + "</td>";
                    html += "<td>" + guesses[i].weight + " kilogram</td>";
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
             * Update the graph with the newest data
             */
            function updateGraph() {
                // Define the for loop index, and the minimum/maximum value
                var i;
                var min = 0;
                var max = 25;

                // Get the chart options
                chartOptions = chart.options;

                // Generate the chart data
                var data = [];
                for(i = min; i <= max; i++)
                    data.push(0);
                for(i = 0; i < getGuessesCount(); i++)
                    data[Math.round(guesses[i].weight) - min]++;

                // Set the chart data
                chart.series[0].setData(data, true);
            }

            // Update everything on start
            updateTable();
            updateGuessCounter();
            updateGraph();

            // Bind to the channel to process updates
            channel.bind('client-newGuess', function(data) {
                // Add the data to the list of guesses
                guesses.push({firstName: data.firstName, lastName: data.lastName, weight: data.weight});
                //alert('CLIENT: Name: ' + data.firstName + ' ' + data.lastName + '; Weight: ' + data.weight + ' KG');

                updateTable();
                updateGuessCounter();
                updateGraph();
            });
        }
    }
});