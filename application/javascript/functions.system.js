/**
 * Pseudo function for create mata items
 * NOTE its possible to use this base function for other area items
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 */
function createItem() {
	getAreaData('meta/index', 'meta');
	getAreaData('cats/index', 'cats');
}

/**
 * Refresh areas
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {Array} aData
 */
function refreshAreas(aData, sNamespace) {
	var sTmp = '';
	var bHasId;
	$.each(aData[sNamespace], function(sName, aValues) {

		// CODE FOR Everything

		sTmp += window[sNamespace + 'Build'](sName, aValues);

	});
	$('.area_loader').fadeOut(1000);
	$("#" + sNamespace + " .items").html(sTmp);

	if(sNamespace == 'meta') {
		$('.dragger').draggable({
			drag : doDrag.action,
			revert : 'invalid',
			appendTo : '#overflow_middle',
			scroll : false,
			helper : 'clone'
		});
	}
}

/**
 * Communication
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 */
function resetCOMVars() {
	// Reset defined Vars
	sRequestSender = '';
	sGlobalDatas = '';
}

/**
 * Pseudo system function for inital load/reset components
 * Place all systemstart required code here
 */
function initAll() {
	// reset textareas
	$.each($('textarea'), function(noTxtArea, oTxtArea) {
		if(iBrowser != 0) {
			$(oTxtArea).val($(oTxtArea).val(''));
		}

	});
	preloadButtonSprites();
	createItem();
	preventSubmit();
}

// Clean Input fields
$('input[type=text]').live('focusout', function() {
	$(this).val(filterBadChars($(this).val()));
});
// AJAX communication management
$("input[type=submit]").live('click', function() {
	var callFrom = $(this).parent().parent().parent().attr('id');
	sRequestSender = callFrom + '/' + this.className;
	$(this).parent().parent().css('margin-top', '0px');
	if($(this).hasClass('add')) {
		var sNewElement = $(this).parent().children("input[type=text]").val();
		var targetUrl = $(this).parent().parent().parent().attr("action");
		sCallId = $(this).parent().parent().parent().attr('id');
		//var datas = 'callId=' + sCallId + '&' + 'op=' + this.className + '&' + $($(this).parent().parent().parent()).serialize();
		if(sCallId == 'meta') {
			openDialog('done', 'Add new metaitem', extendMeta(sNewElement), 'Ok', 'Abort', true, true);
			return false;
		}
		if(sCallId == 'cats') {
			openDialog('done', 'Add new Category', configureCat($('#cats .controll_panel .head_form .area').val()), 'Ok', 'Abbort', true, true);
			return false;
		}
		if(sCallId == 'comp') {
			if($('#source').val() != 'Source' && !$('source').hasClass('tia_red') && $('div#comp .controll_panel .head_form .area').val() != 'new Composite') {
				$('#source, div#comp .controll_panel .head_form .area').removeClass('tia_red');

				openDialog('done', 'Add new Composite', configureComp($('#comp .controll_panel .head_form .area').val()), 'Ok', 'Abbort', true, true);
			} else {
				$('#source').addClass('tia_red');
				if($('div#comp .controll_panel .head_form .area').val() == 'new Composite') {
					$('div#comp .controll_panel .head_form .area').addClass('tia_red');
				}
			}
			return false;
		} else {
			alert('Not Implemented yet');
		}
	} else {
		if(!$(this).hasClass('edit') && !$(this).hasClass('tidyDesk') && !$(this).hasClass('add_cat')) {
			createRequest(this);
			openDialog('type', 'headline', 'text', 'greenlabel', 'redlabel', false, false);
		}
	}

	return false;
});
/**
 * Preload sprite images automatically for direct change on event
 */
function preloadButtonSprites() {
	if($('.button').length != 0) {
		/* var sSpriteImagePath = $('.button').css('background-image');
		 sSpriteImagePath = sSpriteImagePath.replace(/url\(/g, '').replace(/"/g, '');
		 sSpriteImagePath = sSpriteImagePath.replace(/'/g, '').replace(/\)/g, '');

		 oSpriteDefault.src = sSpriteImagePath;
		 oSpriteHover.src = sSpriteImagePath.replace(/_default/, '_hover');
		 oSpriteClick.src = sSpriteImagePath.replace(/_default/, '_click');
		 oSpriteActive.src = sSpriteImagePath.replace(/_default/, '_active');

		 sDefaultImage = oSpriteDefault.src;*/
	}
}

/**
 * This function group button object for build complex input fields
 * Containing more than one .button object and on evnet each button should
 * get the same state as the currently selected one just add in the parent
 * element in dom the classname "btn_group"
 *
 * @param {Object} oButtonObject
 * @return {Object} Groupe of Objects or Overgiven Object
 */
function groupButtons(oButtonObject) {
	if($(oButtonObject).parent().hasClass('btn_group')) {
		oButtonObject = $(oButtonObject).parent().children('.button');
	}

	return oButtonObject;
}