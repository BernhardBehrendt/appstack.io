/**
 * Search if property sKey is a primary
 *
 * @param {String} sKey
 * @param {Array} aKnownPrimarys
 */
function inArray(item, aKeys) {
    for (var i = 0; i < aKeys.length; i++) {
        if (item == aKeys[i]) {
            return true;
        }
    }
    return false;
}

/**
 * Checks if given property is an array
 * @param {Mixed} obj
 * @return {Array}
 */
function isArray(mProperty) {
   if (mProperty.constructor.toString().indexOf("Array") == -1)
      return false;
   else
      return true;
}
