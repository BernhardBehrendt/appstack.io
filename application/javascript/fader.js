var bPlugInit = false; (function($) {
	$.fn.fadable = function() {

		function calcSubBudget(oFader, iSubBudget) {
			var oAvails = $(oFader).parent().parent().children('.avails').children('input');
			var oSubBudget = $(oFader).parent().parent().children('.subbudget').children('input');
			var oSummary = $(oFader).parent().parent().children('.summary');
			var iLeft = parseInt($(oAvails).val());
			var iRight = parseInt($(oSubBudget).val());
			var iSum = $(oSummary).val();

			$(oSubBudget).val(Math.round(iSum * iSubBudget));

			if(iSubBudget < 1) {
				$(oAvails).val(Math.round(iSum * (1 - iSubBudget)));
			} else {
				$(oAvails).val(0);
			}

			validateBudget(oSummary, oAvails, oSubBudget)

		}

		function validateBudget(oSummary, oMine, oSubBudget) {
			if((parseInt($(oMine).val()) + parseInt($(oSubBudget).val())) > parseInt($(oSummary).val())) {
				var iCorrection = (parseInt($(oMine).val()) + parseInt($(oSubBudget).val())) - parseInt($(oSummary).val());
				$(oSubBudget).val(parseInt($(oSubBudget).val()) - iCorrection);

			}
		}

		if(!bPlugInit) {
			$('.fader-knob').click(function() {
				return false;
			});
			$('.push_right').click(function() {
				var oSubbudget = $(this).parent().parent().children('.subbudget').children('input');
				$(oSubbudget).val(parseInt($(oSubbudget).val()) + parseInt($(this).parent().children('input').val()));
				$(this).parent().children('input').val(0);

				$(this).parent().parent().children('.budget-fader').children('a').animate({
					left : 270
				}, 500);

				return false;
			});
			$('.push_left').click(function() {
				var oAvails = $(this).parent().parent().children('.avails').children('input');
				$(oAvails).val(parseInt($(oAvails).val()) + parseInt($(this).parent().children('input').val()));
				$(this).parent().children('input').val(0);

				$(this).parent().parent().children('.budget-fader').children('a').animate({
					left : 0
				}, 500);

				return false;
			});
			var sEnterValue = false;
			$('.mine').focusin(function() {
				sEnterValue = $(this).val();
				$(this).val('');
			}).keydown(function(e) {
				if(e.keyCode == 13) {
					$(this).blur();
				}
			}).focusout(function() {

				var iNewVal = parseInt($(this).val());

				if(isNaN(iNewVal)) {
					iNewVal = sEnterValue;
				}

				var iMaxVal = parseInt($(this).parent().parent().children('.summary').val());

				var oHis = $(this).parent().parent().children('.subbudget').children('input');

				if(iNewVal > iMaxVal) {
					iNewVal = iMaxVal;
				}

				if(iNewVal < 0) {
					iNewVal = 0;
				}

				var iSubbudget = iMaxVal - iNewVal;
				var oFader = $(this).parent().parent().children('.budget-fader').children('.fader-knob');

				$(this).val(iNewVal);
				$(oHis).val(iSubbudget);

				var iFadeTo = Math.round((iSubbudget / iMaxVal) * 270);

				if(iFadeTo > 270) {
					iFadeTo = 270;
				}
				if(iFadeTo < 0) {
					iFadeTo = 0;
				}

				$(oFader).animate({
					left : iFadeTo
				}, 500);

			});
			$('.his').focusin(function() {
				sEnterValue = $(this).val();
				$(this).val('');
			}).keydown(function(e) {
				if(e.keyCode == 13) {
					$(this).blur();
				}
			}).focusout(function() {

				var iNewVal = parseInt($(this).val());

				if(isNaN(iNewVal)) {
					iNewVal = sEnterValue;
				}

				var iMaxVal = parseInt($(this).parent().parent().children('.summary').val());

				var oMine = $(this).parent().parent().children('.avails').children('input');

				if(iNewVal > iMaxVal) {
					iNewVal = iMaxVal;
				}

				if(iNewVal < 0) {
					iNewVal = 0;
				}

				var iMineBudget = iMaxVal - iNewVal;
				var oFader = $(this).parent().parent().children('.budget-fader').children('.fader-knob');

				$(this).val(iNewVal);

				$(oMine).val(iMineBudget);

				var iFadeTo = Math.round((iNewVal / iMaxVal) * 270);

				if(iFadeTo > 270) {
					iFadeTo = 270;
				}
				if(iFadeTo < 0) {
					iFadeTo = 0;
				}

				$(oFader).animate({
					left : iFadeTo
				}, 500);

			});
			$('.budget-fader').click(function(e) {
				var xPosition = e.originalEvent.layerX  - 6;
				var oThis = $(this);

				if(xPosition < 0) {
					xPosition = 0;
				}
				if(xPosition > 270) {
					xPosition = 270;
				}
				
				$(this).children('.fader-knob').animate({
					left : (xPosition)
				}, 500, function() {
					var iCurPos = parseInt(xPosition);

					if(iCurPos > 270) {
						$(this).addClass('max');
					} else {
						$(this).removeClass('max');
					}
					if(iCurPos < 0) {
						$(this).addClass('min');
					} else {
						$(this).removeClass('min');
					}
					if(iCurPos <= 270 && iCurPos >= 0) {
						calcSubBudget($(this), iCurPos / 270);
					}
				});
			});
			$.each($('.fader-knob'), function(i, oFader) {
				var oAvails = $(oFader).parent().parent().children('.avails').children('input');
				var oSubBudget = $(oFader).parent().parent().children('.subbudget').children('input');
				var oSummary = $(oFader).parent().parent().children('.summary');
				var iLeft = parseInt($(oAvails).val());
				var iRight = parseInt($(oSubBudget).val());
				var iSum = $(oSummary).val();
				var iMoveTo = ((iRight / iSum) * 270);

				$(oFader).animate({
					left : iMoveTo
				}, 500);
			});
			$('.fader-knob').draggable({
				axis : 'x',
				drag : function() {
					var iCurPos = parseInt($(this).css('left').replace(/px/, ''));

					if(iCurPos > 270) {
						$(this).addClass('max');
					} else {
						$(this).removeClass('max');
					}
					if(iCurPos < 0) {
						$(this).addClass('min');
					} else {
						$(this).removeClass('min');
					}
					if(iCurPos <= 270 && iCurPos >= 0) {
						calcSubBudget($(this), iCurPos / 270);
					}
				},
				stop : function() {
					var iCurPos = parseInt($(this).css('left').replace(/px/, ''));
					if(iCurPos > 270) {
						$(this).css('left', 270 + 'px');
					}
					if(iCurPos < 0) {
						$(this).css('left', 0 + 'px');
					}
				}
			});
			bPlugInit = true;
		}
	}
})(jQuery);
