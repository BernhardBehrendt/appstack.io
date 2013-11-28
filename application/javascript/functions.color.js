/**
 * Creates an solid hex string by on overgive rgb value e.g.
 * A) rgb(255, 255, 255) returns #FFF
 * B) rgb(16, 153, 240) returns #1099F0
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {string} rgb
 * @return {string}
 *
 */
function rgb2hex(rgb) {

    rgb = rgb.replace(/rgb/g, '').replace(/\(/g, '').replace(/\)/g, '').split(',');

    function hex(x) {
        hexDigits = new Array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F");
        return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
    }

    var postReturn = hex(rgb[0]) + hex(rgb[1]) + hex(rgb[2]);

    var a = postReturn.charAt(0);
    var b = postReturn.charAt(1);
    var c = postReturn.charAt(2);
    var d = postReturn.charAt(3);
    var e = postReturn.charAt(4);
    var f = postReturn.charAt(5);

    if (a == b && c == d && e == f) {
        postReturn = a + c + e;
    }

    return "#" + postReturn;
}

