// IE FIX there is a problem with Internet Explorer onchange event listener
// Solved with these construct
$('.meta_values').live("click", function() {
	$(this).focus();
	
});
$(".meta_values").live('change', function() {
		var metaname = $(this).parent().parent().children("span.metaname").html();
		$(this).after(addNumOfFileds(this, this.value, metaname));
	}).attr("onchange", function() {
		var metaname = $(this).parent().parent().children("span.metaname").html();
		$(this).after(addNumOfFileds(this, this.value, metaname));
	});
// GROUP MANAGEMENT
$("#defineMeta input").keyup( function() {
	if ($(this).val().length > 0 && !IsNumeric($(this).val())) {
		$(this).val('');
	}
}).focusin( function() {
	$(this).val('');
}).focusout( function() {
	var sInput = $(this).val();
	var iLength = 0;
	var sOptions = '<option value="0">Bitte w&auml;len...</option>';

	if (IsNumeric(sInput)) {
		$.each(aTest['meta'], function(sName, aValues) {
			sOptions += '<option value="' + aValues['ID'] + '">' + sName + '</option>';
			iLength++;
		});
		if (iLength < sInput) {
			sInput = iLength;
		}
		$(this).parent().children('select').remove();
		for (var i = 0; i < sInput; i++) {
			$(this).after('<select name="container[grp_meta_' + i + ']">' + sOptions + '</select>');
		}
	}
});
// Switch Item Workspaces [view/edit]
$("input.edit, input.save").live('click', function() {
	var matched = false;
	var oSender = $(this);
	$(this).parent().children('input[type=submit], input[type=button], .item_description, .value').toggle();
});