/**
 * Function prepare server gotten data for processing on stage
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 *
 * @param {Object} JSON Stream aPreJson
 */
function metaPrepare(aPreJson) {

	var aPostJson = '{';
	var bSkip = false;

	$.each(aPreJson, function(i, aItem) {
		$.each(aItem, function(sSubspace, data) {
			// Various Subnamespacves can be placed here for different subopeations

			if(sSubspace == 'message') {
				openDialog(data.type, data.headline, data.text, data.greenlabel, data.redlabel, true, false);
				sRequestSender = 'close';
				bSkip = true;
			}
		});
		if(bSkip) {
			bSkip = false;
			return false;
		}

		var sSeperator = (i > 0) ? ',' : '';
		var bIsFirst = true;
		aPostJson += sSeperator + '"' + aItem['name'] + '" :{';
		$.each(aItem['properties'], function(j, aDefaults) {
			var iCurId = false;

			$.each(aDefaults, function(sName, sDefault) {
				sSeperator = (!bIsFirst) ? ',' : '';
				bIsFirst = false;
				if(inArray(sName, aKnownPrimarys)) {
					aPostJson += sSeperator + '"' + sDefault + '" : "' + sName + '"';
					iCurId = sDefault;
				} else {
					aPostJson += sSeperator + '"' + sName + '_' + iCurId + '" : "' + sDefault + '"';
				}
			});
		});
		aPostJson += '}';
	});
	aPostJson += '}';

	return $.parseJSON(aPostJson);
}

/**
 * Add Metavalues and their default(s) to in confirmation window
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {String} metaName
 *
 * @return {String}
 */
function extendMeta(metaName) {
	metaName = metaName.replace(/ /g, '-').toLowerCase();
	var sTmpSelect = '<select class="meta_values tia_green"><option value="0">Bitte w&auml;hle die Anzahl der Werte</option>';
	for(var i = 0; i < 11; i++) {
		sTmpSelect += '<option value="' + i + '">' + i + ' Wert(e)</option>';
	}
	sTmpSelect += '</select>';
	var sExtend = '<span class="metaname tia_lighter_grey">' + metaName + '</span><p>' + sTmpSelect + '</p>';

	return sExtend;
}

/**
 * add a number of fields (name = defaultvalue)
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 * @param {Object} target
 * @param {Integer} iNum
 * @param {String} metaname
 *
 * @return {String}
 */
function addNumOfFileds(target, iNum, metaname) {

	$(target).parent().children("form").remove();

	var sMetaVal = '<br><br>';
	for(var i = 0; i < iNum; i++) {
		sMetaVal += '<input class="mvalname tia_green" type="text" value="Name" name="' + metaname + '[' + i + '][name]"/>';
		sMetaVal += '<input class="mval tia_green" type="text" value="Wert" name="' + metaname + '[' + i + '][value]"/>';
	}
	return '<form id="defineMeta" action="#" method="post">' + sMetaVal + "</form>";
}

/**
 * Render Metaitemdata into HTML
 * This is function which is also called dynamic so dont delete or change this if you
 * are not firm in this systempart
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {Object} sName
 * @param {Object} aValues
 */
function metaBuild(sName, aValues) {
	bHasId = false;
	var sTmp = '';
	var iIdMeta = false;
	var sDragShow = ($('.close_comp:visible').length > 0) ? '' : 'dn';
	sTmp += '<div id="meta_{idmeta}" class="item tia_blue clearfix" style=""><span class="metaname">' + sName + '</span><span class="metaid tia_pink tia_very_small">({idmeta})</span>';
	sTmp += '<a class="dragger ' + sDragShow + ' fl_left" href="#"></a>';
	$.each(aValues, function(sParam, eValue) {
		if(inArray(eValue, aKnownPrimarys)) {
			sTmp += '<input type="hidden" value="' + eValue + '"  name="container[' + sParam + ']"/>';
			bHasId = true;
			iCurId = sParam;
		} else {
			if(sParam.search(/valname/) != -1) {

				var iDfaultInt = sParam.split('_');
				iDfaultInt = iDfaultInt[1];

				var sDefault = aValues['valdef_' + iDfaultInt];
				sDefault = (sDefault == 'null') ? '' : sDefault;
				sTmp += '<span class="item_description tia_pink tia_small" style="display:none;">' + ((eValue.length > 25) ? eValue.substring(0, 25) + '...' : eValue) + '</span>' + '<input type="text" value="' + sDefault + '" class="value tia_blue" name="container[' + sParam + ']"/>';
			}
			if(sParam.search(/metas_idmeta_/) != -1) {
				sTmp += '<input class="idmeta" type="hidden" value="' + eValue + '"  name="container[' + sParam + ']"/>';
				//bHasParent = true;
				if(!iIdMeta) {
					iIdMeta = eValue;
				}
			}
		}
	});
	sTmp += '<input type="button" class="edit" value=""/>';
	sTmp += '<input type="submit" class="save" value=""/>';
	sTmp += '<input type="submit" class="delete" value=""/>';
	sTmp += '</div>';

	if($('#overflow_middle #metas_idmeta' + iIdMeta).length == 0) {
		sTmp = sTmp.replace(/{idmeta}/g, iIdMeta);
	} else {
		sTmp = '';
	}

	if(!bHasId) {
		sTmp = '';
		return sTmp;
		window.document.write('Error wrong Itemdata');

	}
	bHasId = false;
	return sTmp;
}