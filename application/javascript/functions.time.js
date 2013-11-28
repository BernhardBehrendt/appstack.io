/**
 * Return the current timestamp in ms requirde for
 * internet explorer for better cache handling
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @return {String}timestamp as getvar
 */
function getTimeStamp() {
    return '';//?uts=' + new Date().getTime();
}

/**
 * Converts a mysql Timestamp into a readable Date (system default)
 * @param {Object} sDateString
 */
function parseDate(sDateString, sAlternative) {

    sDateString = (sDateString !== null) ? sDateString : sAlternative;

    var aDateTime = sDateString.split(' ');

    var aDate = aDateTime[0].split('-');
    var aTime = aDateTime[1].split(':');

    // FORMAT DATE
    return aDate[2] + '.' + aDate[1] + '.' + aDate[0] + ' ' + aTime[0] + ':' + aTime[1];
}
