function configureCompSource() {
	// HOLE HIER DIE KONFIGURATIONSDATEN FÜR DIE LISTE UND DAS GENERIEREN DES ASSISTENTEN

	var sConfigSrc = '<div id="source_container" class="clearfix comp_value tia_green"> ';
	sConfigSrc += '<input type="radio" class="select_mode fl_left" value="assistent" id="usewiz" name="configsrc">';
	sConfigSrc += '<label for="usewiz" class="select_label fl_left">Provider</label>';
	sConfigSrc += '<select class="target_reference" id="provider" disabled="disabled">';
	sConfigSrc += '<option value="0">Please select...</option>';
	sConfigSrc += '</select><div id="wizzard"></div><br/><br/>';

	sConfigSrc += '<input type="radio" class="select_mode fl_left" value="self" id="own" checked="checked" name="configsrc">';
	sConfigSrc += '<label for="own" class="select_label fl_left">own source</label>';
	sConfigSrc += '<input type="text" id="src" value="' + $('#source').val() + '" name="src" class="C_src tia_green" />';
	sConfigSrc += '</div>';

	sRequestSender = 'comp/addsource';

	return sConfigSrc;
}

function configureComp() {
	sRequestSender = 'comp/add';
	var sOptionlistTpl = '<option value="{catid}">{catname}</option>';
	var sOptions = '';

	var iMarginLeft = parseInt($('#catmatrix').css('margin-left').toLowerCase().replace(/px/, '')) - 300;
	var iNumberDepth = ((iMarginLeft / 300) * -1);
	$.each($('#depth_' + iNumberDepth + ' .category:visible'), function(iCurCat, oDepthCat) {
		var iCatId = $(oDepthCat).attr('id').replace(/cat_/, '');
		sOptions += sOptionlistTpl.replace(/\{catid\}/, iCatId).replace(/\{catname\}/, $(oDepthCat).children('.inner_contain').children('.catname').text());
	});

	var sConfigComp = '<div id="source_container" class="clearfix comp_value tia_green">';
	sConfigComp += '<input type="radio" class="select_mode fl_left" value="in_child" id="in_empty" name="configcomp"> ';
	sConfigComp += '<label for="in_empty" class="select_label fl_left">Insert composite in a child of current category</label>';
	sConfigComp += '<select class="target_reference tia_select" id="destination" disabled="disabled">';
	sConfigComp += '<option value="0">Please select...</option>';
	sConfigComp += sOptions;
	sConfigComp += '</select><br/><br/>';
	sConfigComp += '<input type="radio" checked="checked" class="select_mode fl_left" value="in_current" id="in_empty" name="configcomp"> ';
	sConfigComp += '<label for="in_empty" class="select_label fl_left">Insert composite in current category</label>';
	sConfigComp += '</div>';

	return sConfigComp;
}

function infoComp(iComposite, oComps) {

	var oComposite = false;

	$.each(oComps, function(iCompo, oComp) {
		if(oComposite === false) {
			if(oComp.idcomposite == iComposite) {
				oComposite = oComp;
				iComp = iCompo;
				return false
			}
		} else {
			return false;
		}
	});

	if(parseInt(oComposite.pub) === 0) {
		var sPubSwich = '<input type="checkbox" name="public" id="public" value="1"/><label for="public" class="tia_pink">private</label>';
	} else {
		var sPubSwich = '<input type="checkbox" name="public" id="public" checked="checked" value="1"/><label for="public" class="tia_green">public</label>';
	}

	var sTemplate = '<div id="catinfo" class="clearfix">';
	sTemplate += '<div class="tia_green tia_bold row_name fl_left">Name</div><div class="tia_blue row_value fl_right">' + oComposite.name + '</div>';
	sTemplate += '<input value="' + oComposite.idcomposite + '" id="ident" type="hidden"/>';
	sTemplate += '<div class="tia_green tia_bold row_name fl_left">Created</div><div class="tia_blue row_value fl_right">' + parseDate(oComposite.created) + '</div>';
	sTemplate += '<div class="tia_green tia_bold row_name fl_left">Modified</div><div class="tia_blue row_value fl_right">' + parseDate(oComposite.modified, oComposite.created) + '</div>';

	sTemplate += '<div class="tia_green tia_bold row_name fl_left clearfix">Source<a href="#" class="dn fl_right source_relocate"></a></div><div class="tia_blue row_value fl_right"><input type="text" value="' + oComposite.source + '" class="compsrc"/></div>';
	sTemplate += '<div class="tia_green tia_bold row_name fl_left">Status</div><div class="tia_blue row_value fl_right">' + sPubSwich + '</div>';
	sTemplate += "</div>";

	return sTemplate;
}

function renameComp(iComposite) {
	var sOutput = '';
	sOutput += '<h2 id="new_compname" class="fl_left db">Enter new composite name</h2>';

	sOutput += '<div id="rename_comp" class="clearfix tia_green">';
	sOutput += '<input id="rename_' + iComposite;
	sOutput += '" type="text" name="name" value="New composite name" class="comprename tia_green fl_left"/>';

	sOutput += '</div>';
	return sOutput;
}

function appendMeta(iComposite, iMeta) {
	$.ajax('../../comp/append', {
		data : {
			'composite' : iComposite,
			'meta' : iMeta
		},
		success : function(sReturn) {
			$('body').append(sReturn);
		}
	});
}

function loadCompMetas(iComposite) {
	$(this).removeClass('dragger_target_bg');
	var sTplCompMeta = '<div class="item tia_blue clearfix" id="metas_idmeta_{id_comp_meta}">';

	sTplCompMeta += '<span class="metaname">{metaname}</span><span class="metaid tia_pink tia_very_small">({id_comp_meta})</span>';
	sTplCompMeta += '{comp_meta_values}';
	sTplCompMeta += '<input type="button" value="" class="edit"/>';
	sTplCompMeta += '<input type="submit" value="" class="save"/>';
	sTplCompMeta += '<input type="submit" value="" class="delete"/>';
	sTplCompMeta += '</div>';

	var sTplCompMetaVal = '<input type="hidden" name="container[{id_comp_meta_val}]" value="id_mnv"/>';
	sTplCompMetaVal += '<input type="hidden" name="container[metas_idmeta_{id_comp_meta_val}]" value="{id_comp_meta}" class="idmeta"/>';
	sTplCompMetaVal += '<span style="display:none;" class="item_description tia_pink tia_small">{valname}</span>';
	sTplCompMetaVal += '<input type="text" name="container[valname_{id_comp_meta_val}]" class="value tia_blue" value="{valdef}"/>';

	var sCompMetas = '';

	$.getJSON('../../comp/load', {
		'composite' : iComposite
	}, function(oCompMetas) {

		var sMetas = '';
		$.each(oCompMetas, function(i, oPart) {
			var sMetaPart = sTplCompMeta;

			var sCMVals = '';
			$.each(oPart.values, function(ib, oMeta) {
				var sCMValsPart = sTplCompMetaVal;
				sCMValsPart = sCMValsPart.replace(/{id_comp_meta_val}/g, ib);
				$.each(oMeta, function(ic, oMetaVal) {
					sCMValsPart = sCMValsPart.replace(/{valname}/g, ((ic.length > 25) ? ic.substring(0, 25) : ic));
					sCMValsPart = sCMValsPart.replace(/{valdef}/g, oMetaVal);
				});
				sCMVals += sCMValsPart;
			});

			sMetaPart = sMetaPart.replace(/{comp_meta_values}/, sCMVals);
			sMetaPart = sMetaPart.replace(/{id_comp_meta}/g, i);
			sMetaPart = sMetaPart.replace(/{metaname}/g, oPart.name);
			sMetas += sMetaPart;

			$('.items #meta_' + i).remove();

		});
		$('#overflow_middle').html(sMetas);
	});
}
