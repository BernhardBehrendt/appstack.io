/**
 * Prepare Json Category data for further processing
 * Dummy function for categorys because there is a clean result from php
 *
 * @param {Object} oPreJson
 */
function catsPrepare(oPreJson) {
	oTree = oPreJson;
	return oPreJson;
}

/**
 * function for rendering category tree
 */
function catRenderTree() {
	// Fadein scroll up/down icon
	$.each($('.up, .down'), function(i, object) {

		// Fetch current category ID
		var sId = $(this).parent().attr('id').replace(/cat_/, '');
		var oCatOps = $(this).parent().children('.inner_contain').children('.catops');
		// Handling for the up button (am I children of 0)
		if($(this).hasClass('up')) {
			if(!$(this).parent().hasClass('child_of_0')) {
				$(this).removeClass('tia_cursor_none');
				$(this).css('display', 'block');
			} else {
				$(this).addClass('tia_cursor_none');
				$(this).css('opacity', '0');
			}
		}

		// handling for down button (am i parent)
		if($(this).hasClass('down')) {
			if($('.child_of_' + sId).length != 0) {
				$(this).removeClass('tia_cursor_none');
				$(this).css('display', 'block');
				$(oCatOps).children('.cat_delete').css('opacity', '0.5').addClass('tia_cursor_none');
				$(oCatOps).children('.cat_comps').css('opacity', '1').removeClass('tia_cursor_none');
				$(oCatOps).children('.cat_rename').css('opacity', '1').removeClass('tia_cursor_none');
				$(oCatOps).children('.cat_infos').css('opacity', '1').removeClass('tia_cursor_none');
			} else {
				$(this).addClass('tia_cursor_none');
				$(this).css('opacity', '0');
				$(oCatOps).children('.cat_delete').css('opacity', '1').removeClass('tia_cursor_none');
				$(oCatOps).children('.cat_comps').css('opacity', '1').removeClass('tia_cursor_none');
				$(oCatOps).children('.cat_rename').css('opacity', '1').removeClass('tia_cursor_none');
				$(oCatOps).children('.cat_infos').css('opacity', '1').remove('tia_cursor_none');
			}
		}
	});
}

/**
 *
 * @param {Object} sName
 * @param {Object} aValue
 */
function catsBuild(sName, aValue) {
	var sCatmatrix = ' ';
	var iMaxDepth = 0;
	var iAreaWidth = 300;
	// Set the ID of the root level

	iInCat = aValue.idcategory;
	$("#catmatrix").removeAttr('style');

	$.each(aValue.properties, function(iDepth, aCategoryData) {
		sCatmatrix += '<div class="depth" id="depth_' + iDepth + '">';

		var sTmp = '';
		$.each(aCategoryData, function(iCat, aCategory) {
			if(aCategory.pub == 1) {
				var sPublic = 'cat_public';
				var sStatus = 'public';
			} else {
				var sPublic = 'cat_private';
				var sStatus = 'private';
			}
			sTmp += '<div class="category child_of_' + aCategory.child_of + ' clearfix" id="cat_' + aCategory.ident + '">';
			sTmp += '<a class="fl_left up button" href="#"></a>';
			sTmp += '<div class="fl_left clearfix inner_contain tia_small tia_blue">';
			sTmp += '<div class="catname fl_left cat_info cat_info_left">';
			sTmp += ((aCategory.name.length > 18) ? aCategory.name.substring(0, 18) + '...' : aCategory.name) + ' <span class="tia_pink tia_very_small">(' + aCategory.ident + ')</span>';
			sTmp += '</div>';
			sTmp += '<div class="catops fl_right cat_info cat_info_right">';
			sTmp += '<a href="#" title="delete" class="cat_delete"></a>';
			sTmp += '<a href="#" title="show composites" class="cat_comps"></a>';
			sTmp += '<a href="#" title="rename" class="cat_rename"></a>';
			sTmp += '<a href="#" title="properties of (' + sStatus + ') category" class="cat_infos ' + sPublic + '"></a>';
			sTmp += '</div>';
			sTmp += '<div class="created_lbl fl_left cat_info cat_info_left">';
			sTmp += '<span class="tgl_created_show">created</span><span class="tia_pink"> | </span><span class="tgl_modified_show">modified</span>';
			sTmp += '</div>';
			sTmp += '<div class="created_modified fl_right cat_info cat_info_right">';
			sTmp += '<span class="tgl_created tia_yellow tia_small dn">' + parseDate(aCategory.created, aCategory.created) + '</span>';
			sTmp += '<span class="tgl_modified tia_yellow tia_small dn">' + parseDate(aCategory.modified, aCategory.created) + '</span>';
			sTmp += '</div>';
			sTmp += '<div class="composites_lbl fl_left cat_info cat_info_left tia_border_none">';
			sTmp += 'Composites';
			sTmp += '</div>';
			sTmp += '<div class="composites_num fl_right cat_info cat_info_right tia_border_none">';
			sTmp += aCategory.comps;
			sTmp += '</div>';
			sTmp += '</div>';
			sTmp += '<a class="fl_right down button" href="#"></a>';
			sTmp += '</div>';
			sCatmatrix += sTmp;
			sTmp = '';
		});
		sCatmatrix += '</div>';
		iMaxDepth = iDepth;
	});
	$("#catmatrix").css('width', iMaxDepth * iAreaWidth + 'px');
	return sCatmatrix + '<script type="text/javascript"> $(\'.category\').fadeOut(250, function(){$(\'.child_of_0\').css(\'display\', \'block\');});$(\'.category\');catRenderTree();</script>';

}

/**
 *
 * @param {Object} sCatName
 */
function configureCat(sCatName) {
	// sCatName = sCatName.replace(/ /g, '_').toLowerCase();

	var aCategories = getCatsInDepth(iCurCatIn, false);
	var aEmptyCats = getCatsInDepth(iCurCatIn, true);

	var sOutput = '';
	sOutput += '<h2 id="new_catname" class="fl_left db">' + sCatName + '</h2>';
	sOutput += '<div class="configure_cat clearfix tia_green">';
	sOutput += '<input type="radio" name="target" id="in_empty" value="in_empty" class="select_mode fl_left"/>';
	sOutput += '<label class="select_label fl_left"for="in_empty">Add as first category in empty category</label>';
	sOutput += '<select disabled="disabled" id="direction_emptycat" class="target_reference tia_select tia_green">';
	sOutput += '<option value="0">Please select...</option> ';

	$.each(aEmptyCats, function(iKey, sName) {
		sOutput += '<option value="' + sName[0] + '">' + sName[1] + '</option>';
	});
	sOutput += '</select>';
	sOutput += '</div>';
	sOutput += '<div class="configure_cat clearfix tia_green">';
	sOutput += '<input type="radio" name="target" id="first_current" value="first" class="select_mode fl_left"/>';
	sOutput += '<label class="select_label fl_left"for="first_current">Add as first category in current category</label>';
	sOutput += '</div>';
	sOutput += '<div class="configure_cat clearfix tia_green">';
	sOutput += '<input type="radio" name="target" id="last_current" value="last" class="select_mode fl_left"/><label class="select_label fl_left"for="last_current">Add as last category in current category</label>';
	sOutput += '</div>';
	sOutput += '<div class="before_behind configure_cat clearfix tia_green">';
	sOutput += '<div class="clearfix"><input type="radio" name="target" id="before" value="before" class="select_mode fl_left"/><label class="select_label fl_left"for="before">Add before selected category in list </label></div>';
	sOutput += '<div class="clearfix"><input type="radio" name="target" value="behind" id="behind" class="select_mode fl_left"/><label class="select_label fl_left"for="behind">Add behind selected category in list</label></div>';
	sOutput += '<select disabled="disabled" id="direction" class="target_reference tia_select tia_green">';
	sOutput += '<option value="0">Please select...</option>';
	$.each(aCategories, function(iKey, sName) {
		sOutput += '<option value="' + sName[0] + '">' + sName[1] + '</option>';
	});
	sOutput += '</select>';
	sOutput += '</div>';

	return sOutput;

}

function renameCat(iCategory) {
	var sOutput = '';
	sOutput += '<h2 id="new_catname" class="fl_left db">Enter new category name</h2>';
	sOutput += '<div id="rename_cat" class="clearfix tia_green">';
	sOutput += '<input id="rename_' + iCategory;
	sOutput += '" type="text" name="name" value="New category name" class="catrename tia_green fl_left"/>';
	sOutput += '</div>';
	return sOutput;
}

function infoCat(iCatId, oTree) {
	var oCategory = false;

	$.each(oTree[0]['properties'], function(iDepth, oDepth) {
		if(oCategory === false) {
			$.each(oDepth, function(iCat, oCat) {
				if(oCat.ident == iCatId) {
					oCategory = oCat;
					return false
				}
			});
		} else {
			return false;
		}
	});
	if(parseInt(oCategory.pub) === 0) {
		var sPubSwich = '<input type="checkbox" name="public" id="public" value="1"/><label for="public" class="tia_pink">private</label>';
	} else {
		var sPubSwich = '<input type="checkbox" name="public" id="public" checked="checked" value="1"/><label for="public" class="tia_green">public</label>';
	}

	var iComposites = $('#cat_' + iCatId + ' .inner_contain .composites_num').text();

	var sTemplate = '<div id="catinfo" class="clearfix">';
	sTemplate += '<div class="tia_green tia_bold row_name fl_left">Name</div><div class="tia_blue row_value fl_right">' + oCategory.name + '</div>';
	sTemplate += '<input value="' + oCategory.ident + '" id="ident" type="hidden"/>';
	sTemplate += '<div class="tia_green tia_bold row_name fl_left">Created</div><div class="tia_blue row_value fl_right">' + parseDate(oCategory.created) + '</div>';
	sTemplate += '<div class="tia_green tia_bold row_name fl_left">Modified</div><div class="tia_blue row_value fl_right">' + parseDate(oCategory.modified, oCategory.created) + '</div>';
	sTemplate += '<div class="tia_green tia_bold row_name fl_left">Composites</div><div class="tia_blue row_value fl_right">' + iComposites + '</div>';
	sTemplate += '<div class="tia_green tia_bold row_name fl_left">Status</div><div class="tia_blue row_value fl_right">' + sPubSwich + '</div>';
	sTemplate += "</div>";

	return sTemplate;
}

function getCatsInDepth(iIdParent, onlyEmpty) {
	var aCategoryList = new Array();

	$.each($('.child_of_' + iIdParent), function(iListNum, oCategory) {
		if(onlyEmpty) {
			if($(oCategory).children('.down').css('opacity') == 0) {
				aCategoryList[aCategoryList.length] = new Array($(oCategory).attr('id').replace(/cat_/g, ''), $(oCategory).children('.inner_contain').children('.catname').text());
			}
		} else {
			aCategoryList[aCategoryList.length] = new Array($(oCategory).attr('id').replace(/cat_/g, ''), $(oCategory).children('.inner_contain').children('.catname').text());
		}
	});
	return aCategoryList;
}

function catNavigate(oCallUpDown, iMoveToCat, iDuration) {
	if($(oCallUpDown).hasClass('down')) {
		if($('#catmatrix').css('opacity') == '1') {
			var iMarginLeft = parseInt($('#catmatrix').css('margin-left').toLowerCase().replace(/px/, '')) - 300;
			var iEnd = parseInt($('#catmatrix').css('width').toLowerCase().replace('px', ''));
			var iCatmatrixMrgLft = parseInt($('#catmatrix').css('margin-left').replace(/px/, ''));

			var iNumberDepth = ((iMarginLeft / 300) * -1) + 1;
			var sIdCat = $(oCallUpDown).parent().attr('id').replace(/cat_/, '');
			iCurCatIn = sIdCat;
			aDepthMarginTop[iNumberDepth - 1] = parseInt($('#catmatrix').css('margin-top'));
			$('#depth_' + iNumberDepth + ' .category:visible').css('display', 'none');
			$('#depth_' + iNumberDepth + ' .category').removeClass('filtered');
			if((iMarginLeft * -1) != iEnd) {
				if($('#catmatrix .depth .child_of_' + sIdCat).length != 0) {
					$('.child_of_' + sIdCat).css('display', 'block');
					$('#catmatrix').css('opacity', '0.4');
					$('#catmatrix').animate({
						'margin-left' : iMarginLeft + 'px',
						'margin-top' : 0 + 'px'
					}, iDuration, function() {
						$('#catmatrix').animate({
							'opacity' : '1.0'
						}, 100, function() {
							if(iMoveToCat !== false) {
								moveToCat(iMoveToCat);
							}
						});
					});
				}
			}
		}
	}
	if($(oCallUpDown).hasClass('up')) {
		if($('#catmatrix').css('opacity') == '1') {
			var iCatmatrixMrgLft = parseInt($('#catmatrix').css('margin-left').replace(/px/, ''));
			var iNumberDepth = (iCatmatrixMrgLft * -1) / 300;

			var iChildOf = $(oCallUpDown).parent().attr('class').split(' ')[1].replace(/child_of_/, '');
			iCurCatIn = $('#depth_' + iNumberDepth + ' #cat_' + iChildOf).attr('class').split(' ')[1].replace(/child_of_/, '');

			var iMarginLeft = parseInt($('#catmatrix').css('margin-left').toLowerCase().replace(/px/, '')) + 300;
			var oLevelCats = $(oCallUpDown).parent().parent().children('.category');

			if(!$(this).hasClass('child_of_0')) {
				$('#catmatrix').css('opacity', '0.4');
				$('#catmatrix').animate({
					'margin-left' : iMarginLeft + 'px',
					'margin-top' : aDepthMarginTop[iNumberDepth]
				}, iDuration, function() {
					$('#catmatrix').animate({
						'opacity' : '1.0'
					}, 100, function() {
						if(iMoveToCat !== false) {
							moveToCat(iMoveToCat);
						}
					});
				});
			}
		}
	}
}

function moveToCat(iIdCat) {
	if($('#cat_' + iIdCat).length != 0) {
		var iTargetDepth = parseInt($('#cat_' + iIdCat).parent().attr('id').replace(/depth_/, ''));
		var iCatmatrixMrgLft = parseInt($('#catmatrix').css('margin-left').replace(/px/, ''));
		var iNumberDepth = (iCatmatrixMrgLft * -1) / 300;
		var aMovePath = new Array();
	}

	if(iTargetDepth == iNumberDepth + 1) {
		if(iCatMove == 'down') {
			if($('.child_of_' + iCurCatIn).length != 0) {
				catNavigate($('#cat_' + iTmp + ' a.down'), false, 5);
				iTmp = false;
			}
		}
		if(iCatMove == 'up') {

		}
		if(iCatMove === false) {
			if($('.child_of_' + iCurCatIn).length != 0) {
				catNavigate($('#cat_' + iCurCatIn + ' a.down'), false, 5);
			} else {
				catRenderTree();
			}
		}
		iCatMove = false;

		return true;
	}
	if(iTargetDepth > iNumberDepth + 1) {
		// MOVE DOWN TO CAT
		var iChildOf = parseInt($('#cat_' + iIdCat).attr('class').split(' ')[1].replace(/child_of_/, ''));
		var bBreak = false;
		var aPath = new Array();
		iCatMove = 'down';
		aPath[0] = iChildOf;
		while(bBreak === false) {
			var iChildOf = parseInt($('#cat_' + iChildOf).attr('class').split(' ')[1].replace(/child_of_/, ''));
			aPath[aPath.length] = iChildOf;
			if(iChildOf === 0) {
				bBreak = true;
			}
		}
		aPath.reverse();
		catNavigate($('#cat_' + aPath[iNumberDepth + 1] + ' a.down'), iIdCat, 5);

	}
	if(iTargetDepth < iNumberDepth + 1) {
		//MoveUp
		iCatMove = 'up';
		catNavigate($('#depth_' + (iNumberDepth + 1) + ' .category:eq(0) a.up'), iIdCat, 5);
	}
}

function showCompositesInCat(iIdCat) {
	$('#complist .composite').remove();
	$('#complist').showCatComps(iIdCat);
}

$.fn.showCatComps = function(iIdCat) {
	// calcuate dimensions
	var oThis = $(this);
	$(this).css({
		position : 'absolute',
		width : '1px',
		height : '1px',
		display : 'block'
	});
	$(this).css('z-index', $('#cats').css('z-index'));
	$(this).css('height', ($('#cats').height() - 75));
	$(this).css('left', (parseInt($('#cats').css('left').toLowerCase().replace(/px/, '').replace(/;/, '')) + $('#cats').width() + 6));
	$(this).css('top', (parseInt($('#cats').css('top').toLowerCase().replace(/px/, '').replace(/;/, '')) + 41));
	$(this).css('background-color', '#434343');

	var sNewTpl = '<div id="composite_{id}" class="composite clearfix">';
	sNewTpl += '<div class="fl_left clearfix inner_contain tia_small tia_blue">';
	sNewTpl += '<div class="compname fl_left comp_info comp_info_left">{name} <span class="tia_pink tia_very_small">({id})</span></div>';
	sNewTpl += '<div class="compops fl_right comp_info comp_info_right">';
	sNewTpl += '<a class="comp_delete" title="delete" href="#"></a>';
	sNewTpl += '<a class="comp_rename" title="rename" href="#"></a>';
	sNewTpl += '<a href="#" title="move composite" class="comp_move"></a>';
	sNewTpl += '<a href="#" title="duplicate composite" class="comp_duplicate"></a>';
	sNewTpl += '<a class="comp_infos comp_{pub}" title="{title}" href="#"></a>';
	sNewTpl += '</div>';
	sNewTpl += '<div class="created_lbl fl_left comp_info comp_info_left">';
	sNewTpl += '<span class="tgl_created_show">created</span><span class="tia_pink"> | </span>';
	sNewTpl += '<span class="tgl_modified_show">modified</span>';
	sNewTpl += '</div>';
	sNewTpl += '<div class="created_modified fl_right cat_info cat_info_right">';
	sNewTpl += '<span class="tgl_created tia_yellow tia_small dn">{created}</span>';
	sNewTpl += '<span class="tgl_modified tia_yellow tia_small dn">{modified}</span></div>';
	sNewTpl += '</div>';
	sNewTpl += '<a class="fl_right db comp_open" href="#"></a>';

	var sPostOut = '';
	var sTplLayout = '';

	$('#complist .composite').remove();

	$.getJSON('../../comp/index' + getTimeStamp(), 'parent=' + iIdCat, function(data) {
		if(data.length == 0) {
			$(oThis).animate({
				width : '1px',
				opacity : 0
			}, 500);
		} else {
			// Stores the last loaded compositelist global for other applicants
			oCompList = data;

			$.each(data, function(iComp, oComp) {
				var sPartOut = sNewTpl.replace(/\{id\}/g, oComp.idcomposite);
				sPartOut = sPartOut.replace(/{name\}/g, ((oComp.name.length > 14) ? oComp.name.substring(0, 13) + '...' : oComp.name));
				sPartOut = sPartOut.replace(/{created\}/g, parseDate(oComp.created));
				sPartOut = sPartOut.replace(/{modified\}/g, parseDate(oComp.modified, oComp.created));
				sPartOut = sPartOut.replace(/{pub\}/g, (oComp.pub == 0) ? 'private' : 'public');
				sPartOut = sPartOut.replace(/{title\}/g, 'properties of ' + ((oComp.pub == 0) ? 'private' : 'public') + ' composite ');

				$('#complist').prepend(sPartOut);
			});
		}
	});
	$(this).animate({
		width : '310px',
		opacity : 1
	}, 500, function() {
		$(this).children('.scroll_panel').fadeIn(250, function() {
			$('#comp').animate({
				'left' : '644px',
				'top' : '10px'
			}, 500);
			$('#meta').animate({
				'left' : '956px',
				'top' : '10px'
			}, 500);
			$('#groups').animate({
				'left' : '956px',
				'top' : '10px'
			}, 500);
		});
	});
	$('.complist_close, .add_cat, .up, .down').live('click', function() {
		$('.close_comp').trigger('click');
		$('#complist').animate({
			width : '1px',
			opacity : 0
		}, 500, function() {
			$(this).children('.scroll_panel').fadeOut(250);
			$('#comp').animate({
				'left' : '332px',
				'top' : '10px'
			}, 500);
			$('#meta').animate({
				'left' : '644px',
				'top' : '10px'
			}, 500);
			$('#groups').animate({
				'left' : '956px',
				'top' : '10px'
			}, 500);
			$('.composite').remove();
		});
	});
	$(window).resize(function() {
		$('#complist').css('height', ($('#cats').height() - 75));
	});
};
/**
 * Tree Map plugin for jquery
 * @param {Object} oTree
 */
$.fn.treeMap = function(oTree, sMode) {

	// Configuration
	var oThis = $(this);
	var iFirstMatch = 0;
	var iInCat = iCurCatIn;
	var sTree = '';
	var sAddButton = '<a title="insert in {fullname}" href="#" class="insert_comp db sprite button"></a>';
	var sDumpOut = '<div id="treeMap" style="position:relative;background-color:rgba(0, 0, 0, 0.1;">{objects}</div>';
	var sCatModel = '<div id="treeMapCat_{id}" style="left:{left}px; top:{top}px;" class="treeMapCat {dynclass}">' + ((sMode == 'comp_move') ? sAddButton : '') + '{name}</div>';
	var sStyle = ' background-color: rgba(0, 0, 0, 0.8);';
	sStyle += 'cursor: pointer;';
	sStyle += 'position: relative;';
	sStyle += 'overflow: hidden;';
	sStyle += 'left: 0;';
	sStyle += 'top: 0;';
	sStyle += 'z-index: 100001;';

	var aTreeMatrix = new Array();

	function TM_render(oTree, iStartAt, iInitCord, iLookForChildsOf, aRendered) {

		var iCurDepth = iStartAt;
		var iMaxDepths = oTree.length;

		$.each(oTree[iStartAt], function(iCurCat, oCategory) {
			if(!iLookForChildsOf || oCategory[2] == iLookForChildsOf) {

				var bUpdateCounter = true;
				var iCatsRendered = aRendered.length;

				aRendered[iCatsRendered] = oCategory;
				aRendered[iCatsRendered][4] = iCurDepth + 1;
				aRendered[iCatsRendered][5] = iInitCord;

				if(iFirstMatch == 0) {
					iFirstMatch = oCategory[0];
				}

				if((iStartAt + 1) < iMaxDepths) {
					if(!TM_lookForChild(oTree[iStartAt][iCurCat][0], oTree[iStartAt + 1])) {

					} else {
						TM_render(oTree, iStartAt + 1, iInitCord, oCategory[0], aRendered);
						iInitCord = TM_nextCord(aRendered);
						bUpdateCounter = false;

					}
					if(bUpdateCounter) {
						iInitCord++;
					}
				} else {
					iInitCord++;
				}
			}
		});
		return aRendered;
	}

	function TM_lookForChild(iIdParent, aTreePart) {

		var bMatch = false;

		$.each(aTreePart, function(iCurCat, oCat) {

			if(!bMatch && iIdParent == oCat[2]) {
				bMatch = true;
			}
		});
		return bMatch;
	}

	function TM_nextCord(oTreePart) {
		var iCountMax = 1;
		$.each(oTreePart, function(iCats, oCat) {

			if(oCat[5] > iCountMax) {
				iCountMax = oCat[5];
			}
		});
		return parseInt(iCountMax) + 1;
	}


	$.each(oTree, function(i, oRootNode) {
		$.each(oRootNode.properties, function(iNumDepth, oDepths) {
			var iCurDepth = iNumDepth - 1;
			aTreeMatrix[iCurDepth] = new Array();
			$.each(oDepths, function(iNumCat, oCategory) {
				aTreeMatrix[iCurDepth][iNumCat] = new Array();
				aTreeMatrix[iCurDepth][iNumCat][0] = oCategory.ident;
				aTreeMatrix[iCurDepth][iNumCat][1] = oCategory.name;
				aTreeMatrix[iCurDepth][iNumCat][2] = oCategory.child_of;
				aTreeMatrix[iCurDepth][iNumCat][3] = oCategory.depth;
				aTreeMatrix[iCurDepth][iNumCat][4] = iNumDepth;
				aTreeMatrix[iCurDepth][iNumCat][5] = iNumCat;
				aTreeMatrix[iCurDepth][iNumCat][6] = oCategory.pub;
				aTreeMatrix[iCurDepth][iNumCat][7] = $('#cat_' + oCategory.ident + ' .inner_contain .composites_num').text();

			});
		});
	});
	var aTreeMatrixOut = TM_render(aTreeMatrix, 0, 1, false, new Array());
	var iWidthFactor = 1;
	var iHeightFactor = 1;
	var cDraw = '';
	$.each(aTreeMatrixOut, function(iCat, aCat) {

		var sTmpTpl = sCatModel;

		if(aCat[6] == 1) {
			var sPublic = 'pubcat';
		} else {
			var sPublic = '';
		}
		sTmpTpl = sTmpTpl.replace(/\{id\}/g, aCat[0] + '_' + aCat[2] + '_' + aCat[3]);
		sTmpTpl = sTmpTpl.replace(/\{fullname\}/g, aCat[1]);
		sTmpTpl = sTmpTpl.replace(/\{name\}/g, (aCat[1].length > 13) ? aCat[1].substring(0, 10) + '... <b>(' + aCat[7] + ')</b>' : aCat[1] + ' <b>(' + aCat[7] + ')</b>');
		sTmpTpl = sTmpTpl.replace(/\{dynclass\}/g, ((iInCat == aCat[2]) ? 'activeTree ' + sPublic : sPublic));
		sTmpTpl = sTmpTpl.replace(/\{top\}/g, aCat[5] * 30);
		sTmpTpl = sTmpTpl.replace(/\{left\}/g, aCat[4] * 132);

		// FUTURE Add draw line from an to oteher categories
		//cDraw += " $('#linedraw').drawLine(0, 0, " + (aCat[4] * 170) + ", " + (aCat[5] * 49) + ", {color: '#00ff00'});";

		iWidthFactor = (aCat[4] > iWidthFactor) ? aCat[4] + 1 : iWidthFactor;
		iHeightFactor = (aCat[5] > iHeightFactor) ? aCat[5] + 1 : iHeightFactor;
		sTree += sTmpTpl;

	});
	if(sMode == 'navigate') {
		moveToCat(iFirstMatch);
	}

	$(oThis).attr('style', sStyle);
	$(oThis).css('height', $(document).height());
	$(oThis).css('width', $(document).width());

	$(window).resize(function() {
		$(oThis).css('height', $(window).height());
		$(oThis).css('width', $(window).width());
	});
	$('.treeMapCat').live('click', function() {
		if(sMode == 'navigate') {

			var aPosData = $(this).attr('id').replace(/treeMapCat_/, '').split('_');

			moveToCat(aPosData[0]);

			$(oThis).fadeOut(500);
		}
		return false;

	});
	$(oThis).html(sDumpOut.replace(/\{objects\}/, sTree));

	//$('#treeMap').append('<div id="linedraw" class="canvas" style="z-index:10;"></div>');
	//$('#linedraw').css('height', $(document).height());
	//$('#linedraw').css('width', $(document).width());
	if(cDraw.length > 0) {
		//eval(cDraw);
	}
	$('#treeMap').css({
		width : ((iWidthFactor + 1) * 170) + 'px',
		height : ((iHeightFactor + 1) * 49) + 'px'
	});

	// Scroll
	$(document).mousemove(function(e) {
		var iMapWidth = $('#treeMap').width();
		var iMapHeight = $('#treeMap').height();
		var iPageWidth = $(document).width();
		var iPageHeight = $(document).height();
		var iMouseX = e.pageX;
		var iMouseY = e.pageY;

		if(iMapWidth > iPageWidth) {

			var iDifference = iMapWidth - iPageWidth;
			iDifference = iDifference + 170;

			$('#treeMap').css('left', (Math.floor(iDifference * (iMouseX / iPageWidth)) * -1) + 'px');
		}

		if(iMapHeight > $(document).height()) {

			var iDifference = iMapHeight - iPageHeight;
			iDifference = iDifference + 49;

			$('#treeMap').css('top', (Math.floor(iDifference * (iMouseY / iPageHeight)) * -1) + 'px');
		}

	});
	// Exit plugin
	$(oThis).live('click', function() {
		$(this).fadeOut(500, function() {

			$(document).unbind('mousemove');
			$(window).unbind('resize');
			$(oThis).empty();
			if(iInCat != 0) {
				moveToCat(iInCat);
			}
		});
	});
	$(this).fadeIn(500);
};
var oCompositeWindow = {};
