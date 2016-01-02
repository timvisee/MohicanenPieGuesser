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