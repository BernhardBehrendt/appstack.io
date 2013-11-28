var iTmpComp = false;
// COMPOSITES______________________________________________________________________________________________________
$('#source').focusin(function() {
	openDialog('info', 'HEADLINE', configureCompSource(), 'Ok', 'Cancel', true, true);
});
$('.source_item').live('click', function() {
	$('.source_item').removeClass('sel_source');
	$(this).addClass('sel_source');
});
$('input[name="configsrc"]').live('change', function() {
	if ($(this).val() == 'assistent') {
		$('#provider').animate({
			'opacity' : '1'
		}, 100, function() {
			$('#src').animate({
				'opacity' : '0.4'
			}, 100);
		});
		$('#src').attr('readonly', true);
		$('#provider').attr('disabled', false);

		$.ajax('../../source/list', {
			success : function(sServices) {
				$('#provider').html(sServices);
			}
		});

	}
	if ($(this).val() == 'self') {
		$('#wizzard').empty();
		$('#confirm').css('height', $(window).height());
		$('#src').attr('readonly', false);
		$('#provider').val(0);
		$('#provider').attr('disabled', true);

		$('#src').animate({
			'opacity' : '1'
		}, 100, function() {
			$('#provider').animate({
				'opacity' : '0.4'
			}, 100);
		});
	}
});
$('#provider').live('change', function() {
	if ($(this).val() != 0) {
		$.ajax('../../source/' + $(this).val(), {
			success : function(sWizzard) {
				$('#wizzard').html(sWizzard);
			}
		});
	} else {
		// DELETE EACH CONFIGURATION PANEL
		$('#wizzard').empty();
	}
});
$('#mode').live('change', function() {
	if ($(this).val() == 'content') {
		$('#attr').fadeOut(250);
	} else {
		$('#attr').fadeIn(250);
	}
});
$('#config_source_xpath input').live('focusout', function() {
	$('#location, #element, #attr, #limit').attr('readonly', true).css('opacity', '0.3');
	if ($('#attr').val() == 'access attribute' || $('#attr').val().length == 0) {
		var sMode = 'content';
	} else {
		var sMode = 'attr';
	}
	if ($('#location').val().length > 4) {
		$('#confirm').css('height', $(window).height());
		$.ajax('../../source/' + $('#provider').val(), {
			data : 'mode=' + sMode + '&location=' + $('#location').val() + (($('#element').val() != 'select (x)html tag') ? '&element=' + $('#element').val() : '') + (($('#attr').val() == 'access attribute') ? '' : '&attr=' + $('#attr').val()) + '&limit=' + $('#limit').val(),
			success : function(sWizzard) {
				$('#wizzard').html(sWizzard);
				$('#confirm').css('height', $(document).height());
				$('#confirm').css('width', $(window).width());
				$('#location, #element, #attr, #limit').attr('readonly', false).css('opacity', '1');
			}
		});
	}
});
$('input[name="configcomp"]').live('change', function() {
	if ($(this).val() == 'in_child') {
		$('#destination').animate({
			'opacity' : '1'
		}, 100);

		$('#destination').attr('disabled', false);

	}
	if ($(this).val() == 'in_current') {

		$('#destination').attr('disabled', true);

		$('#destination').animate({
			'opacity' : '0.4'
		}, 100);

	}
});
$("#source").val('Source');

$('.comp_delete').live('click', function() {
	$('.close_comp').trigger('click');
	var sSubmitData = '';
	var iIdCat = $(this).parent().parent().parent().attr('id').replace(/composite_/g, '');

	sSubmitData += '&comp=' + iIdCat;
	sRequestSender = 'comp/remove';

	sGlobalDatas = sSubmitData;

	createRequest(false);

	return false;
});
$('.comp_duplicate').live('click', function() {
	if (!bLockCompDuplicate) {
		bLockCompDuplicate = true;
		$('.close_comp').trigger('click');
		var sSubmitData = '';
		var iIdCat = $(this).parent().parent().parent().attr('id').replace(/composite_/g, '');

		sSubmitData += '&comp=' + iIdCat;
		sRequestSender = 'comp/duplicate';

		sGlobalDatas = sSubmitData;

		createRequest(false);
	}
	return false;
});
$('.comp_rename').live('click', function() {
	var iComposite = parseInt($(this).parent().parent().parent().attr('id').replace(/composite_/, ''));
	openDialog('info', 'Rename composite', renameComp(iComposite), 'Ok', 'Abort', true, true);
	sRequestSender = 'comp/rename';
});
$('.comp_move').live('click', function() {
	$('#catmap').treeMap(oTree, 'comp_move');
	iTmpComp = parseInt($(this).parent().parent().parent().attr('id').replace(/composite_/, ''));
});
$('.comp_infos').live('click', function() {
	var iComposite = $(this).parent().parent().parent().attr('id').replace(/composite_/g, '');
	openDialog('info', '../../composite information', infoComp(iComposite, oCompList), 'Ok', 'Cancel', true, true);
	sRequestSender = 'comp/info';

	return false;
});
$('.insert_comp').live('click', function() {
	var iTargetId = false;
	iTargetId = parseInt($(this).parent().attr('id').split('_')[1]);
	var sSubmitData = '&comp=' + iTmpComp + '&destination=' + iTargetId;
	iTmpComp = false;
	sRequestSender = 'comp/insert';

	sGlobalDatas = sSubmitData;

	createRequest(false);

	$('#catmap').fadeOut(500, function() {
		$(document).unbind('mousemove');
		$('#catmap').empty();
	});
	return false;
});
//alert(oCompList[iComp].name);

$('.compsrc').live('keyup', function() {
	//console.log(oCompList[iComp].source);
	if ($(this).val() != oCompList[iComp].source) {
		$('.source_relocate').fadeIn(250);
	} else {
		$('.source_relocate').fadeOut(250);
	}
});
$('#public').live('change', function() {

});
$('.source_relocate').live('click', function() {

	var sSource = $('.compsrc').val();
	var iComposite = oCompList[iComp].idcomposite;

	if (sSource != oCompList[iComp].source) {

		var sSubmitData = '';

		sSubmitData += '&source=' + sSource;
		sSubmitData += '&composite=' + iComposite;
		sRequestSender = 'comp/relocate';

		sGlobalDatas = sSubmitData;

		oCompList[iComp].source = sSource;

		createRequest(false);
	}
	return false;

});
$('.comp_open').live('click', function() {
	if ($('.close_comp:visible').length > 0) {
		$('#overflow_middle .item').remove();
		getAreaData('meta/index', 'meta');
	}
	var iMetas = $('.dragger').length;
	$('.dragger').fadeIn(250, function() {
		if (iMetas == 1) {

			$('.dragger').draggable({
				drag : doDrag.action,
				revert : 'invalid',
				appendTo : '#overflow_middle',
				scroll : false,
				helper : 'clone'
			});
		}
		iMetas--;
	});
	var iComposite = parseInt($(this).parent().attr('id').replace(/composite_/, ''));
	loadCompMetas(iComposite);
	iCurComp = iComposite;
	$.each(oCompList, function(iCompo, oComp) {
		if (parseInt(oComp.idcomposite) == iComposite) {
			iComposite = iCompo;
		}
	});
	$('#overflow_middle').css('background-color', '#333');
	$('#choosen_comp_name').text(oCompList[iComposite].name);
	var sSource = oCompList[iComposite].source;
	if (sSource.length > 32) {
		sSource = sSource.substr(0, 29) + '...';
	}
	$('#choosen_comp_source').text(sSource);
	$('#comp .head_form .area, #comp .head_form .add, #source').fadeOut(250);
	$('#choosen_comp_name, #choosen_comp_source, #save_comp, #restore_comp').fadeIn(250);
	$('#comp .controll_panel .head_form .close_comp').fadeIn(250, function() {
		$('#overflow_middle').css('margin-top', Math.floor($('#comp .controll_panel').height() + 5) + 'px');
	});
	return false;
});
$('#comp .controll_panel .head_form .close_comp:visible').live('click', function() {
	if ($('#comp .controll_panel .head_form .close_comp:visible').length > 0) {
		$(this).fadeOut(250, function() {
			$('#overflow_middle .item').remove();
			$('#overflow_middle').css('background-color', '#616161');

			getAreaData('meta/index', 'meta');

			$('.dragger').fadeOut(250);
			$('#choosen_comp_name').empty();
			$('#choosen_comp_source').empty();
			$('#comp .head_form .area, #comp .head_form .add, #source').fadeIn(250);
			$('#choosen_comp_name, #choosen_comp_source, #save_comp, #restore_comp').fadeOut(250);
		});
	}
	return false;
});
$('.use_as_source').live('click', function() {
	$('#location').val($('#' + $(this).parent().attr('id') + ' input[type="hidden"]').val());
	$('#location').trigger('focusout');
	return false;
});
$('#restore_comp').live('click', function() {
	loadCompMetas(iCurComp);
});
$('#overflow_middle').droppable({
	drop : function(event, ui) {
		oMoveMeta = false;
		iMeta = parseInt(ui.draggable.parent().attr('id').replace(/meta_/, ''));
		appendMeta(iCurComp, iMeta);
		$(this).removeClass('dragger_target_bg');
	}
});
