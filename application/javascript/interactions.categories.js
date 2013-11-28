// CATEGORIES______________________________________________________________________________________________________
$('.tgl_created_show, .tgl_modified_show').live('mouseover', function() {

	$(this).addClass('tia_yellow');

	if($(this).hasClass('tgl_created_show')) {
		$(this).parent().parent().children('.created_modified').children('.tgl_created').fadeIn(250);
	}
	if($(this).hasClass('tgl_modified_show')) {
		$(this).parent().parent().children('.created_modified').children('.tgl_modified').fadeIn(250);
	}
}).live('mouseout', function() {

	$(this).removeClass('tia_yellow');

	if($(this).hasClass('tgl_created_show')) {
		$(this).parent().parent().children('.created_modified').children('.tgl_created').fadeOut(10);
	}
	if($(this).hasClass('tgl_modified_show')) {
		$(this).parent().parent().children('.created_modified').children('.tgl_modified').fadeOut(10);
	}
});
$('.cat_rename').live('click', function() {
	if(!$(this).hasClass('tia_cursor_none')) {
		var iCategory = $(this).parent().parent().parent().attr('id').replace(/cat_/g, '');

		openDialog('info', 'Rename categorie', renameCat(iCategory), 'Ok', 'Abort', true, true);
		sRequestSender = 'cats/rename';
	}
	return false;
});
$('.cat_info').live('click', function() {
	if(!$(this).hasClass('tia_cursor_none')) {
		var iCategory = $(this).parent().parent().attr('id').replace(/cat_/g, '');
		openDialog('info', 'Category information', infoCat(iCategory, oTree), 'Ok', 'Abort', true, true);
		sRequestSender = 'cats/info';
	}
	return false;
});
// Category Management
$('.category').live('mouseover', function() {
	$(this).children('.inner_contain').addClass('tia_pink');
	$(this).children('.inner_contain').removeClass('tia_blue');
}).live('mouseout', function() {
	$(this).children('.inner_contain').addClass('tia_blue');
	$(this).children('.inner_contain').removeClass('tia_pink');
});
// delete category
$('.cat_delete').live('click', function() {
	var sSubmitData = '';
	var iIdCat = $(this).parent().parent().parent().attr('id').replace(/cat_/g, '');
	sSubmitData += '&category=' + iIdCat;
	sRequestSender = 'cats/delete';
	sGlobalDatas = sSubmitData;

	createRequest(false);

	return false;
});
// Scroll into category
$('#catmatrix .depth a.down').live('click', function() {
	catNavigate($(this), false, 500);
	return false;
});
// Scroll upper category
$('#catmatrix .depth a.up').live('click', function() {
	catNavigate($(this), false, 500);
	return false;
});
$('input[name=target]').live('change', function() {
	if($(this).val() == 'behind' || $(this).val() == 'before') {
		$('#direction').animate({
			'opacity' : '1'
		}, 250, function() {
			$(this).removeAttr('disabled');
		});
	} else {
		$('#direction').animate({
			'opacity' : '0.2'
		}, 250, function() {
			$(this).attr('disabled', 'disabled');
			$(this).val('0');
		});
	}

	if($(this).val() == 'in_empty') {
		$('#direction_emptycat').animate({
			'opacity' : '1'
		}, 250, function() {
			$(this).removeAttr('disabled');
		});
	} else {
		$('#direction_emptycat').animate({
			'opacity' : '0.2'
		}, 250, function() {
			$(this).attr('disabled', 'disabled');
			$(this).val('0');
		});
	}

});

$(document).ready(function() {
	
	$('.add_cat').live('click', function() {
		$('#catmap').treeMap(oTree, 'navigate');

		return false;
	});

	$('.cut, .cat_comps').live('click', function() {
		var iCat = 0;
		if($(this).hasClass('cut')) {
			iCat = (iCurCatIn == 0) ? iInCat : iCurCatIn;
		}

		if($(this).hasClass('cat_comps')) {
			iCat = $(this).parent().parent().parent().attr('id').replace(/cat_/g, '');
		}

		showCompositesInCat(iCat);

		return false;
	});
});
