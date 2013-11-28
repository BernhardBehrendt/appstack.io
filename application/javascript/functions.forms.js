function createSelectList(aOptions, sName, sClasses) {
    sOptions = '';
    $.each(aOptions, function(mKey, sValue) {
        sOptions += '<option value="' + sValue + '">' + sValue + '</option>';
    });
    return '<select name="' + sName + '" id="' + sName + '" class="' + sClasses + '">' + sOptions + '</select>';
}
