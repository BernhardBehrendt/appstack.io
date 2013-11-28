$(".abort").click( function() {
	if (sRequestSender == 'comp/add' || sRequestSender =='comp/addsource') {
		$('#comp .controll_panel .head_form:eq(0) .area').val('new Composite');
		$('#source').val('Source');

	}
	closeConfirmation();
});
// Default confirmation Controller
$(".confirm").live('click', function() {
	// Shows the loader
	showConfirmationLoader();
	if (sRequestSender == 'meta/add') {

		var targetForm = $("form#defineMeta");
		var sSubmitData = '';
		var bLockNext = false;
		$.each(targetForm.children("input[type=text]"), function(i, oObject) {
			var oCurObjectAttr = $(oObject).attr('name');

			if (oCurObjectAttr.search(/name/) != -1) {
				var sValue = $(oObject).val();
				if (sValue != 'Name') {
					sSubmitData += '&' + oCurObjectAttr + '=' + $(oObject).val();
					bLockNext = false;
				} else {
					bLockNext = true;
				}
			}
			if (oCurObjectAttr.search(/value/) != -1 && !bLockNext) {
				var sValue = $(oObject).val();
				if (sValue != 'Wert') {
					sSubmitData += '&' + oCurObjectAttr + '=' + sValue;
				}
			}
		});
		sGlobalDatas = sSubmitData;
		if (sSubmitData.length > 0) {

			changeConfirmationHead('Running');
			changeConfirmationMain(sRequestSender);

			$("div#message_headline h1").removeClass("tga_red");
			createRequest(false);
		} else {
			if (!$("div#message_headline h1").hasClass('tga_red')) {
				$("div#message_headline h1").addClass("tga_red");
				changeConfirmationHead('There were no properties defined');
			} else {
				$("div#message_headline h1").removeClass("tga_red");
				closeConfirmation();
			}
		}
	}
	if (sRequestSender == 'cats/add') {
		if ($('input[name=target]:checked').length != 0) {
			var sMode = ($('input[name=target]:checked').val() == 'in_empty') ? 'first' : $('input[name=target]:checked').val();
			var sSubmitData = '';
			sSubmitData += '&name=' + $('#new_catname').text();
			sSubmitData += '&mode=' + sMode;

			if ((sMode == 'before' || sMode == 'behind') && $('#direction').val() != '0') {
				sSubmitData += '&direction=' + $('#direction').val();
			} else {
				if (sMode != 'before' && sMode != 'behind') {
					if ($('input[name=target]:checked').val() == 'in_empty') {
						sSubmitData += '&direction=' + $('#direction_emptycat').val();
					} else {
						sSubmitData += '&direction=' + ((iCurCatIn != 0) ? iCurCatIn : iInCat);
					}
				} else {
					alert('error config');
					return false;
				}
			}

			sGlobalDatas = sSubmitData;
			createRequest(false);
		} else {
		}

	}
	if (sRequestSender == 'cats/rename') {
		if ($.trim($('.catrename').val()).length > 0 && $('.catrename').val() != 'New category name') {
			var sSubmitData = 'name=' + $('.catrename').val() + '&';
			sSubmitData += 'direction=' + $('.catrename').attr('id').replace(/rename_/g, '') + '&';

			sGlobalDatas = sSubmitData;
			createRequest(false);
		} else {
			sRequestSender = 'close';
		}
	}
	if (sRequestSender == 'cats/info') {
		if ($('#public').length > 0) {
			var iStatus = ($('#public:checked').length > 0) ? 1 : 0;
			var sSubmitData = 'public=' + iStatus + '&';
			sSubmitData += 'direction=' + $('#ident').val() + '&';

			sGlobalDatas = sSubmitData;
			createRequest(false);
		} else {
			sRequestSender = 'close';
		}
	}
	// Hier speichern
	if (sRequestSender == 'comp/addsource') {
		if ($('#own:checked').length > 0) {
			if ($('#src').length > 0 && $('#src').val() != 'Sourcepath') {
				$('#source').removeClass('tia_red');
				$('#source').val(filterBadChars($('#src').val().replace(/ /g, '_').replace(/	/g, '_')));
				sRequestSender = 'close';
				closeConfirmation();
			} else {
				$('#source').addClass('tia_red');
				$('#source').val('Source');
				sRequestSender = 'close';
				closeConfirmation();
			}
		}
		if ($('#usewiz:checked').length > 0 && $('#preview_source .sel_source .source').length > 0) {
			$('#source').removeClass('tia_red');
			$('#source').val($('#preview_source .sel_source .source').val());
			sRequestSender = 'close';
			closeConfirmation();
		} else {
			$('#own').trigger('click');
			resetConfirmationBtnGreen();
		}

	}
	if (sRequestSender == 'comp/add') {

		var sSubmitData = 'source=' + $('#source').val() + '&';

		if ($('#in_empty:checked').val() == 'in_current') {
			sSubmitData += 'parent=' + ((iCurCatIn == 0) ? iInCat : iCurCatIn) + '&';
		}

		if ($('#in_empty:checked').val() == 'in_child') {

			if ($('#destination').val() != 0) {
				$('#destination').removeClass('tia_border_red');
				sSubmitData += 'parent=' + $('#destination').val() + '&';
			} else {
				$('#destination').addClass('tia_border_red');
				sSubmitData += 'parent=' + ((iCurCatIn == 0) ? iInCat : iCurCatIn) + '&';
			}

		}

		sSubmitData += 'name=' + $('div#comp .controll_panel .head_form .area').val() + '&';

		sGlobalDatas = sSubmitData;

		createRequest(false);
	}
	if (sRequestSender == 'comp/edit') {
		if ($('#src').length > 0 && $('#own:checked').length > 0) {
			var sSubmitData = 'source=' + $('#src').val() + '&';
			sSubmitData += 'parent=' + ((iCurCatIn == 0) ? iInCat : iCurCatIn) + '&';

			sGlobalDatas = sSubmitData;
			createRequest(false);
		} else {
			sRequestSender = 'close';
		}
	}
	if (sRequestSender == 'comp/rename') {
		if ($.trim($('.comprename').val()).length > 0 && $('.comprename').val() != 'New composite name') {
			var sSubmitData = 'name=' + $('.comprename').val() + '&';
			sSubmitData += 'composite=' + filterBadChars($('.comprename').attr('id').replace(/rename_/g, '')) + '&';

			sGlobalDatas = sSubmitData;
			createRequest(false);
		} else {
			sRequestSender = 'close';
		}
	}
	if (sRequestSender == 'comp/remove') {
		if ($('#src').length > 0 && $('#own:checked').length > 0) {
			var sSubmitData = 'source=' + $('#src').val() + '&';
			sSubmitData += 'parent=' + ((iCurCatIn == 0) ? iInCat : iCurCatIn) + '&';

			sGlobalDatas = sSubmitData;
			createRequest(false);
		} else {
			sRequestSender = 'close';
		}
	}
	if (sRequestSender == 'comp/move') {
		if ($('#src').length > 0 && $('#own:checked').length > 0) {
			var sSubmitData = 'source=' + $('#src').val() + '&';
			sSubmitData += 'parent=' + ((iCurCatIn == 0) ? iInCat : iCurCatIn) + '&';

			sGlobalDatas = sSubmitData;
			createRequest(false);
		} else {
			sRequestSender = 'close';
		}
	}
	if (sRequestSender == 'comp/info') {
		if ($('#public').length > 0) {
			if ($('.compsrc:eq(0)').val().length > 0) {
				var iStatus = ($('#public:checked').length > 0) ? 1 : 0;
				var sStatus = ($('#public:checked').length > 0) ? 'public' : 'private';
				var sSubmitData = 'public=' + iStatus + '&';

				sSubmitData += 'composite=' + $('#ident').val() + '&';
				oCompList[iComp].pup = ($(this).attr('checked') === true) ? 1 : 0;

				var sClassSettings = $('#composite_' + $('#ident').val() + ' .inner_contain .compops .comp_infos').attr('class');
				sClassSettings = sClassSettings.replace(/public/g, sStatus).replace(/private/g, sStatus);
				$('#composite_' + oCompList[iComp].idcomposite + ' .inner_contain .compops .comp_infos').attr('class', sClassSettings);

				oCompList[iComp].pub = iStatus;

				sGlobalDatas = sSubmitData;
				createRequest(false);
			} else {
				$('.compsrc').css('background-color', '#f00');
				resetConfirmationBtnGreen();
				return false;
			}
		} else {
			sRequestSender = 'close';
		}
	}
	if(sRequestSender=='user/login') {
		if ($('input[name="usr"]').val()!='Username' && $('input[name="pwd"]').val().length>0) {	
			var sSubmitData = 'username=' + $('input[name="usr"]').val() + '&';
			sSubmitData += 'password=' + $('input[name="pwd"]').val() + '&';

			sGlobalDatas = sSubmitData;
			createRequest(false);
		} else {
			sRequestSender = 'close';
		}
	}
	if(sRequestSender=='user/refresh') {
		window.location.reload();
	}
	if (sRequestSender == 'close') {
		closeConfirmation();
	}
});
$(window).resize( function() {
	if ($('#confirm:visible').length > 0) {
		$('#confirm').css('height', $(window).height());
		$('#confirm').css('width', $(window).width());
	}
});
/*

 $('#confirm:visible').live('click', function(){

 closeConfirmation();

 });

 */