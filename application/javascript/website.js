var sFor = false;
// Action Handling
/**
 * Mainnavigation controll
 */
$('div#navigation_main ul li a').live('click', function() {
    sFor = $(this).text().toLowerCase().replace(/ /g, '');

    $('div#navigation_main ul li a').removeClass('active');
    $(this).addClass('active');

    $("#content").empty();
    contentLoader();
    updateArea(this, '#content', {
        'target': 'content'
    });
    return false;
});
/**
 * Subnavigation controll
 */
$('div#navigation_sub ul li a').live('click', function() {

    $('div#navigation_sub ul li a').removeClass('active');
    $(this).addClass('active');
    if ($(this).attr('target') != '_blank') {
        $("#content").empty();
        contentLoader();
        updateArea(this, '#content', {
            'target': 'content'
        });
        return false;
    }
});
/**
 * After registration confirmation
 * In future this confimation should be etablished
 */
$('a#reg_form_confirm').live('click', function() {
    $("#content").empty();
    contentLoader();
    updateArea(this, '#content', {
        'target': 'content'
    });
    return false;
});
// Ajax Loader functions
/**
 * This function is for anchor elements an automaticaly catch its href
 *
 * @param {Object} oCaller
 * @param {Object} oTarget
 * @param {Array} aData
 *
 */
function updateArea(oCaller, oTarget, aData) {
    if ($(oCaller).text() != 'Home') {
        loadContent($(oCaller).attr('href'), oTarget, aData);
        return false;
    }
}

/**
 * Load html content as get request and place data on overgiven target
 * @param {String} sSource
 * @param {Object} oTarget
 * @param {Array} aData
 */
function loadContent(sSource, oTarget, aData) {
    $.get(sSource, aData, function(data) {
        $(oTarget).html(data);
        return false;
    });
    return false;
}

// Clean Input fields
$('input[type=text]').live('focusout', function() {
    $(this).val(filterBadChars($(this).val()));
});
// Loader animation functions
/**
 * Ajaxloader for content area
 */
function contentLoader() {
    $("#content").html('<div align="center" style="margin:30px 0 50px 0;"><img src="img/loader4.gif"</div>');
}

/**
 * Ajaxloader for subnavigation area
 */
function subNavLoader() {
    $("#navigation_sub").html('<div align="center" style="margin:50px 0 50px 0;"><img src="img/loader3.gif"</div>');
}

/**
 * Ajaxloader for teaser area
 */
function teaserLoader() {
    $("#teasers").html('<div align="center" style="margin:50px 0 50px 0;"><img src="img/loader3.gif"</div>');
}
