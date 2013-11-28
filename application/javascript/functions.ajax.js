/**
 * create Request (default model)
 * Note if overgive an object it will do a self made call
 * Otherwise a predefined call is requider
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {Object} object
 *
 * @return {Bool}
 */
function createRequest(object) {
	var datas = '';
	var targetUrl = '';
	if(object != false) {
		var sCallFrom = $(object).parent().parent().parent().attr('id');
		datas = 'callfrom=' + sCallFrom + '&';
		if(sCallFrom == 'comp' && iCurComp) {
			datas += 'composite=' + iCurComp + '&';
		}
		targetUrl = '../../' + $(object).parent().parent().parent().parent().attr("action") + '/' + $(object).attr('class') + '/';
		//DELETE???var callId = $(object).parent().parent().parent().attr("id");
		//TODO
		if($(object).hasClass('delete')) {
			$.each($(object).parent().children("input, select"), function(i, input) {
				if($(input).val().length >= 1 && $(input).val() != '0' && $(input).attr('name').search(/metas_idmeta/) != -1) {
					datas += $(input).attr('name') + "=" + $(input).val() + "&";
					return false;
				}
			});
		} else {
			$.each($(object).parent().children("input, select"), function(i, input) {
				if($(input).val().length >= 1 && $(input).val() != '0') {
					datas += $(input).attr('name') + "=" + $(input).val() + "&";
				}
			});
		}

	} else {
		if(sRequestSender && sGlobalDatas) {
			datas = sGlobalDatas;
			targetUrl = '../../' + sRequestSender + '/';

		} else {
			alert('wrong called createRequest');
			return false;
		}
	}

	var bQuietCall = false;
	if(sRequestSender == 'comp/relocate') {
		bQuietCall = true;
	}

	resetCOMVars();

	$.ajax({
		type : "POST",
		url : targetUrl + getTimeStamp(),
		data : datas,
		success : function(msg) {
			if(!bQuietCall) {
				openDialog('done', 'Server Return', msg, 'Ok', 'Abort', true, false);
			} else {
				$('#message_text').append(msg);
			}

		}
	});

	return false;
}

/*
 *  Areas Item fetcher
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {String} sSource
 */
function getAreaData(sSource, sNamespace) {
	var sTimestamp = new Date().getTime();
	sSource = '../../' + sSource + getTimeStamp();
	$.getJSON(sSource, function(aItems) {
		if(aItems['data'] != 'false') {
			// Preprocess data for configured namespace
			oStageData[sNamespace] = window[sNamespace + 'Prepare'](aItems);
			refreshAreas(oStageData, sNamespace);
		}
	});
}