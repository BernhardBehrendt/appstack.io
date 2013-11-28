// INITIALIZE
initAll();
$(document).keydown(function(e) {
	if (e.keyCode == 13) {
		$('#confirmed:visible').trigger('click');
	}
});
// Load Spriteconfiguration
$('body').append(configSprites(sSpriteConf));
// Empty defined textfields and set before value on focusout them empty
$(sInputWatcher).live('focusin', function() {
	sFocusinTmp = $(this).val();
	$(this).val('');
}).live('focusout', function() {
	if ($(this).val() == '') {
		$(this).val(sFocusinTmp)
	}
});
// Deskmanagement
// Tidy up the Desk
$(".tidyDesk").click(function() {
	$('.complist_close').trigger('click');
	for (var i = 0; i < layers.length; i++) {
		$(layers[i][0]).animate({
			'top' : layers[i][5]
		}, 550);
		$(layers[i][0]).animate({
			'left' : layers[i][4]
		}, 550, function() {
			if ($('#comlist:visible').length != 0) {
				$('#complist').css('left', (parseInt($('#cats').css('left').toLowerCase().replace(/px/, '').replace(/;/, '')) + $('#cats').width() + 6));
				$('#complist').css('top', (parseInt($('#cats').css('top').toLowerCase().replace(/px/, '').replace(/;/, '')) + 41));
			}
		});
	}
});
$('.filter').keyup(function() {
	var sSearchFrom = $(this).parent().parent().attr('id');
	var sInput = $(this).val();
	switch (sSearchFrom) {
		case 'cats':
			var iCatmatrixMrgLft = parseInt($('#catmatrix').css('margin-left').replace(/px/, ''));
			var iNumberDepth = (iCatmatrixMrgLft * -1) / 300;

			$.each($('#depth_' + (iNumberDepth + 1) + ' .child_of_' + iCurCatIn + ' .inner_contain .catname'), function(i, oCatName) {
				if (sFilterModeCat == 'expr_ncs') {
					var sCatName = ($(oCatName).text().toLowerCase());
				}
				if (sFilterModeCat == 'expr_cs') {
					var sCatName = ($(oCatName).text());
				}
				if (sCatName.search(sInput) == -1) {
					$(oCatName).parent().parent().addClass('filtered');
					$(oCatName).parent().parent().fadeOut(500);
				} else {
					if ($(oCatName).parent().parent().hasClass('filtered')) {
						$("#catmatrix").css('margin-top', '0px');
						$(oCatName).parent().parent().removeClass('filtered');
						$(oCatName).parent().parent().fadeIn(500);
					}
				}
			});
			break;
		case 'complist':
			$('.composite:eq(0)').css('margin-top', '0px');

			$.each($('.compname'), function(iComp, oCompname) {
				if ($(oCompname).text().search(sInput) == -1) {
					$(oCompname).parent().parent().addClass('filtered');
					$(oCompname).parent().parent().fadeOut(250);
				} else {
					if ($(oCompname).parent().parent().hasClass('filtered')) {
						$(oCompname).parent().parent().removeClass('filtered');
						$(oCompname).parent().parent().fadeIn(250);
					}
				}
			});
			break;
		case 'comp':
			$.each($('#overflow_middle .item'), function(i, oMetaName) {
				var sMetaName = $(this).text();
				if (sMetaName.search(sInput) == -1) {
					$(oMetaName).addClass('filtered');
					$(oMetaName).fadeOut(500);
				} else {
					if ($(oMetaName).hasClass('filtered')) {
						$("#overflow_middle .item:eq(0)").css('margin-top', '0px');
						$(oMetaName).removeClass('filtered');
						$(oMetaName).fadeIn(500);
					}
				}
			});
			break;
		case 'meta':
			$.each($('#meta .items .item'), function(i, oMetaName) {
				var sMetaName = $(this).text();
				if (sMetaName.search(sInput) == -1) {
					$(oMetaName).addClass('filtered');
					$(oMetaName).fadeOut(500);
				} else {
					if ($(oMetaName).hasClass('filtered')) {
						$("#meta .items").css('margin-top', '0px');
						$(oMetaName).removeClass('filtered');
						$(oMetaName).fadeIn(500);
					}
				}
			});
			break;
	}
});
$('.filter_sensitivity').click(function() {
	var sCurPosition = $(this).css('background-position');

	if (sCurPosition.search('5px') != -1) {
		sCurPosition = sCurPosition.replace(/5px/, '40px');
		sFilterModeCat = 'expr_ncs';
	} else {
		if (sCurPosition.search('40px') != -1) {
			sCurPosition = sCurPosition.replace(/40px/, '5px');
			sFilterModeCat = 'expr_cs';
		}
	}

	$(this).css('background-position', sCurPosition);

	return false;
});
$('.scroll_up, .scroll_down').live('click', function() {
	scrollHandler($(this));
	return false;
});
// Scrolling arraw
$('#cats, #meta, #complist, #comp').live('mousewheel', function(objEvent, intDelta) {
	// Scroll posi festhalten wenn kleiner dann up wenn gr√∂√üer dann down oder so
	// auf jeden fall diesen wert dann wieder f√ºr den n√§chgsten vergleich speichern
	if (intDelta > 0) {
		scrollHandler($('#' + $(this).attr('id') + ' .scroll_panel .scroll_up'));
	}
	if (intDelta < 0) {
		scrollHandler($('#' + $(this).attr('id') + ' .scroll_panel .scroll_down'));
	}
	iLastScrollCat = intDelta;
});
// images with this class get hover effects sofar there are the required images available
$('.button, .edit, .delete, .save, .plus, .add').live('mouseover', function() {
	var oThis = groupButtons(this);

	if ($(oThis).css('background-image').search('_active') == -1) {
		sDefaultImage = $(oThis).css('background-image');
		$(oThis).css('background-image', sDefaultImage.replace('_default', '_hover'));
	} else {
		sDefaultImage = $(oThis).css('background-image').replace('_active', '_default');
	}
}).live('mouseout', function() {
	var oThis = groupButtons(this);
	$(oThis).css('background-image', sDefaultImage.replace('_hover', '_default'));
}).live('mousedown', function() {
	var oThis = groupButtons(this);
	if ($(oThis).css('background-image').search('_active') == -1) {
		$(oThis).css('background-image', sDefaultImage.replace('_hover', '_click'));
	}
}).live('mouseup', function() {
	var oThis = groupButtons(this);
	if ($(oThis).css('background-image').search('_active') == -1) {
		$(oThis).css('background-image', sDefaultImage.replace('_default', '_hover'));
	}
}).live('click', function() {
	var oThis = groupButtons(this);
	$.each($('.button'), function(i, data) {
		$(this).css('background-image', sDefaultImage);
	});
	$(oThis).css('background-image', sDefaultImage.replace('_default', '_active'));
});
$('.value').live('keydown', function(evt) {

	if (evt.keyCode == 13) {
		$(this).parent().parent().css('margin-top', '0px');
		var callFrom = $(this).parent().parent().attr('id');

		sRequestSender = callFrom + '/save';

		createRequest($(this).parent().children('.save'));
		//openDialog('type', 'headline', 'text', 'greenlabel', 'redlabel', false);
		evt.preventDefault();
		return false;
	}
});
$('#public').live('change', function() {
	if ($(this).attr('checked')) {
		$(this).parent().children('label').text('public').addClass('tia_green').removeClass('tia_pink');
	} else {
		$(this).parent().children('label').text('private').addClass('tia_pink').removeClass('tia_green');
	}
}); 