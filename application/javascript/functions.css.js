/**
 * Configure sprites defined in Spriteconfiguration (string or json)
 *
 * @param {Object} sSpriteConf
 */
function configSprites(sSpriteConf) {
    var jSpriteConf = $.parseJSON("{" + sSpriteConf + "}");
    var sStylesheet = '';
    var sAllStyle = "background-image:url('../../img/s_default.png'); background-repeat: no-repeat; background-color:transparent;";
    var sAllTargets = '';

    $.each(jSpriteConf, function(sKey, sValue) {
        var sSelectorType = '';

        if (sValue.type == 'tag') {
            sSelectorType = '';
        }
        if (sValue.type == 'id') {
            sSelectorType = '#';
        }
        if (sValue.type == 'class') {
            sSelectorType = '.';
        }

        sAllTargets += (sAllTargets.length == 0) ? sSelectorType + sKey : ', ' + sSelectorType + sKey;

        sStylesheet += sSelectorType + sKey + "{";
        sStylesheet += "background-position: " + sValue.xpos + "px " + sValue.ypos + "px;";
        sStylesheet += "width:" + sValue.width + "px;";
        sStylesheet += "height:" + sValue.height + "px;";
        sStylesheet += "}";
    });
    sStylesheet += sAllTargets + "{" + sAllStyle + "}";

    return '<style type="text/css">' + sStylesheet + '</style>';
}

/**
 * Controll layer depth for bring selected / moved layer to foreground
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {String} layer
 *
 * @return {Bool}
 */
function controllDepth(layer) {
    var iLength = layers.length;
    for (var i = 0; i < iLength; i++) {
        if (layers[iLength - 1] == layer) {
            return true;

        } else {
            if (layers[i] == layer) {
                var tmp = layers[iLength - 1];
                layers[iLength - 1] = layer;
                layers[i] = tmp;
                return true;
            }
        }
    }
    return false;
}
