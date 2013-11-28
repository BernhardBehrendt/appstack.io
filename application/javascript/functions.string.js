/**
 * Function filtes bad chars out of an overgiven string for better security
 * @param {String} sIn
 * @return {String}
 */
function filterBadChars(sIn) {
    var iLenBefore = sIn.length;
    var replace = new RegExp(/\/*\.*\:*\,*\;*\<*\>*\!*\"*\¬ß*\$*\%*\&*\/*\(*\)*\=*\?*\`*\`*\^*\'*\[*\]*\\*\**/g);
    var sOut = sIn.replace(replace, '');
    var iLenAfter = sOut.length;
    return sOut;
}
