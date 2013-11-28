// Init Event Listeners
var oCompositeWindow = {};
oCompositeWindow.place = function(event, ui) {
	if($(this).attr('id') == 'cats') {

		if($('#complist').css('left').toLowerCase().replace(/;/, '') != 'auto') {
			$('#complist').css('height', ($('#cats').height() - 75));
			$('#complist').css('left', (parseInt($('#cats').css('left').toLowerCase().replace(/px/, '').replace(/;/, '')) + $('#cats').width() + 6));
			$('#complist').css('top', (parseInt($('#cats').css('top').toLowerCase().replace(/px/, '').replace(/;/, '')) + 41));
		}
	}
};
// INIT DRAGG OBJECTS
$.each(layers, function(i, layer) {
	// Controll Layers
	if(navigator.platform.toLowerCase().search(/arm/) == -1) {
		/*$(layer[0]).draggable({
			drag : oCompositeWindow.place
		});*/
	}
	$(layer[0]).mouseup(function() {
		if($(this).attr('id') == 'cats') {
			if($('#complist:visible').length > 0) {
				$('#complist').css('left', (parseInt($('#cats').css('left').toLowerCase().replace(/px/, '').replace(/;/, '')) + $('#cats').width() + 6));
				$('#complist').css('top', (parseInt($('#cats').css('top').toLowerCase().replace(/px/, '').replace(/;/, '')) + 41));
			}
		}
		// Controll dragability and zindex (set)
		if(controllDepth(layer)) {
			for(var i = 0; i < layers.length; i++) {
				$(layers[i][0]).css('z-index', i);
			}
		}
	});
	// Controll Items
	$(layer[0] + " .item").live('mouseover', function() {
		$(this).css('borderColor', '#FF0084');
		$(this).css('backgroundColor', '#000');

		// Controll possible dragtargets
		$(layer[1]).css('backgroundColor', layer[2]);
		//$(layer[1]).css('paddingTop', '48px');
		/*$(this).parent().parent().draggable({
			'disabled' : true
		});*/
		$(this).parent().parent().removeClass('ui-state-disabled');
	}).live('mouseout', function() {
		$(this).css('borderColor', '#0063DC');
		/*$(this).parent().parent().draggable({
			'disabled' : false,
			drag : oCompositeWindow.place
		});*/
	});
	// Controll Input fields
	$(layer[0] + " .head_form input[type=text]").focusin(function() {
		if(layer[3]) {
			if($(this).val() == layer[3]) {
				$(this).val('');
			}
		}
	}).focusout(function() {
		if($(this).val() == '') {
			$(this).val(layer[3]);
		}
	}).val(layer[3]);
});

$("body").css("overflow-y", "hidden");