/**
 * Redirect to given URL
 * @param {String} sUrl
 */
function redirect(sUrl) {
	if(sUrl) {
		window.location.replace(sUrl);
	}
}

/**
 * Function prevents executing the submit action for serveral defined objects in dom
 */
function preventSubmit() {
	$(sPreventedSubmits).live('keydown', function(evt) {
		if(evt.keyCode == 13) {
			evt.preventDefault();
			return false;
		}
	});
}

/**
 * Scroll handling
 */
function scrollHandler(oUpDown) {

	// oUpDown switch case controlls scroll actions for the defined areas
	// hold the current area
	var sLocation = $(oUpDown).parent().parent().attr('id');

	// Container items eight
	var iContainerHeight = $(oUpDown).parent().parent().children('.items').height();
	switch (sLocation) {
		// Handle Scroll events for cat area
		case 'cats':
			if(!scrollLockCat) {
				scrollLockCat = true;

				var iCatmatrixMrgTop = parseInt($('#catmatrix').css('margin-top').replace(/px/, ''));
				var iCatmatrixMrgLft = parseInt($('#catmatrix').css('margin-left').replace(/px/, ''));

				// Get the current in use depth container
				var iNumberDepth = (iCatmatrixMrgLft * -1) / 300 + 1;
				// height of the current depth container
				var iDepthHeight = $('#depth_' + iNumberDepth).height();
				// height of one category
				var iCatheight = $('.category').height() + 3;
				// height of the category transparent mask
				var iWindowHeight = $('#cats').height() - 208;
				//number of visible categories
				var iVisibleCatsInDepth = Math.round(iWindowHeight / iCatheight);
				//categories on current depth
				var iCategorysInDepth = $('#depth_' + iNumberDepth + ' .child_of_' + iCurCatIn + ':visible').length;

				if($(oUpDown).hasClass('scroll_up')) {
					if(iCatmatrixMrgTop <= 0) {

						var sMarginTop = Math.round(((iWindowHeight / 2) / iCatheight) * iCatheight + (iCatmatrixMrgTop)) + 'px';

						$('#catmatrix').css('opacity', '0.4');

						$('#catmatrix').animate({
							'margin-top' : sMarginTop
						}, 400, function() {
							scrollLockCat = false;
							$('#catmatrix').animate({
								'opacity' : '1.0'
							}, 200);
							// definded in initvars.js and hold margin top of current depth layer
							aDepthMarginTop[iNumberDepth] = parseInt($('#catmatrix').css('margin-top'));

							if(parseInt($('#catmatrix').css('margin-top').replace(/px/, '')) > 0) {
								// Corrects overscrolling (up) set init position as animation
								$('#catmatrix').animate({
									'margin-top' : '0px'
								}, 400, function() {
									scrollLockCat = false;
									aDepthMarginTop[iNumberDepth] = parseInt($('#catmatrix').css('margin-top'));
								});
								// Block multiple opacity fading
								return false;
							}

						});
					}
				}
				if($(oUpDown).hasClass('scroll_down')) {
					var iMaxScroll = (((iCatheight) * iCategorysInDepth) * -1) + (iCatheight * 2);

					if(iCatmatrixMrgTop - iCatheight > iMaxScroll) {
						$('#catmatrix').css('opacity', '0.4');
						var iAnimateTo = Math.round((iWindowHeight / 2)) * -1 + (iCatmatrixMrgTop);
						$('#catmatrix').animate({
							'margin-top' : iAnimateTo + 'px'
						}, 400, function() {
							scrollLockCat = false;

							$('#catmatrix').animate({
								'opacity' : '1.0'
							}, 200);
							aDepthMarginTop[iNumberDepth] = parseInt($('#catmatrix').css('margin-top'));
						});
					} else {
						scrollLockCat = false;
					}

				}
			}
			break;

		case 'meta':
			if(!scrollLockMeta) {
				scrollLockMeta = true;

				var iMetasLength = $('#meta .items .item').length;
				var iMetaVisibleHeight = $('#meta').height() - 131;
				var iCurScroll = Math.round($('#meta .items').css('margin-top').toLowerCase().replace(/px/g, '') * -1);
				var iPxCorrection = 0;
				var iSumHeight = $('#meta .items').height();

				$.each($('#meta .items .item'), function(iNumMeta, oCurMeta) {
					//  iSumHeight += $(oCurMeta).height();
				});
				if($(oUpDown).hasClass('scroll_up')) {
					if(iCurScroll > 0) {
						$('#meta .items').css('opacity', '0.4');
						$('#meta .items').animate({
							'margin-top' : (iCurScroll * -1) + (iMetaVisibleHeight / 2) + 'px'
						}, function() {
							scrollLockMeta = false;
							$('#meta .items').css('opacity', '1.0');
							if(parseInt($('#meta .items').css('margin-top').replace(/px/, '')) > 0) {
								scrollLockMeta = true;
								$('#meta .items').css('opacity', '0.4');
								$('#meta .items').animate({
									'margin-top' : '0px'
								}, function() {
									scrollLockMeta = false;
									$('#meta .items').css('opacity', '1.0');
								});
							} else {
								scrollLockMeta = false;
							}
						});
					} else {
						scrollLockMeta = false;
					}
				}
				if($(oUpDown).hasClass('scroll_down')) {
					if(iCurScroll < (iSumHeight - 131 - iMetaVisibleHeight / 2)) {

						$('#meta .items').css('opacity', '0.4');
						$('#meta .items').animate({
							'margin-top' : (((iMetaVisibleHeight / 2) * -1) - iCurScroll) + 'px'
						}, function() {
							scrollLockMeta = false;
							$('#meta .items').css('opacity', '1.0');
						});
					} else {
						scrollLockMeta = false;
					}
				}
			}
			break;
		case 'complist':

			if(!scrollLockComp && $('#complist .composite').length > 0) {
				scrollLockComp = true;

				var iMetasLength = $('#complist .composite:visible').length;
				var iMetaVisibleHeight = $('#complist').height() - $('#complist .scroll_panel').height();
				var iCurScroll = Math.round($('#complist .composite:visible:eq(0)').css('margin-top').toLowerCase().replace(/px/g, '') * -1);
				var iPxCorrection = 0;
				var iSumHeight = iMetasLength * ($('#complist .composite:visible:eq(0)').height() + 21);

				if($(oUpDown).hasClass('scroll_up')) {
					if(iCurScroll > 0) {
						//$('#complist .composite').css('opacity', '0.4');
						$('#complist .composite:visible:eq(0)').animate({
							'margin-top' : (iCurScroll * -1) + (iMetaVisibleHeight / 2) + 'px'
						}, function() {
							scrollLockComp = false;
							//  $('#complist .composite').css('opacity', '1.0');
							if(parseInt($('#complist .composite:visible:eq(0)').css('margin-top').replace(/px/, '')) > 0) {
								scrollLockMeta = true;
								//  $('#complist .composite').css('opacity', '0.4');
								$('#complist .composite:visible:eq(0)').animate({
									'margin-top' : '0px'
								}, function() {
									scrollLockComp = false;
									//   $('#complist .composite').css('opacity', '1.0');
								});
							} else {
								scrollLockComp = false;
							}
						});
					} else {
						scrollLockComp = false;
					}
				}
				if($(oUpDown).hasClass('scroll_down')) {
					if(iCurScroll < (iSumHeight - $('#complist .scroll_panel').height() - iMetaVisibleHeight / 2)) {

						// $('#complist .composite').css('opacity', '0.4');
						$('#complist .composite:visible:eq(0)').animate({
							'margin-top' : (((iMetaVisibleHeight / 2) * -1) - iCurScroll) + 'px'
						}, function() {
							scrollLockComp = false;
							//  $('#complist .composite').css('opacity', '1.0');
						});
					} else {
						scrollLockComp = false;
					}
				}
			}
			break;

		case 'comp':

			if(!scrollLockCompMeta && $('#overflow_middle .item').length > 0) {
				scrollLockCompMeta = true;

				var iMetasLength = $('#overflow_middle .item:visible').length;
				var iMetaVisibleHeight = $('#overflow_middle').height() - $('#complist .scroll_panel').height();
				var iCurScroll = Math.round($('#overflow_middle .item:visible:eq(0)').css('margin-top').toLowerCase().replace(/px/g, '') * -1);
				var iPxCorrection = 0;
				var iSumHeight = iMetasLength * ($('#overflow_middle .item:visible:eq(0)').height() + 21);

				if($(oUpDown).hasClass('scroll_up')) {
					if(iCurScroll > 0) {
						//$('#complist .composite').css('opacity', '0.4');
						$('#overflow_middle .item:visible:eq(0)').animate({
							'margin-top' : (iCurScroll * -1) + (iMetaVisibleHeight / 2) + 'px'
						}, function() {
							scrollLockCompMeta = false;
							//  $('#complist .composite').css('opacity', '1.0');
							if(parseInt($('#overflow_middle .item:visible:eq(0)').css('margin-top').replace(/px/, '')) > 0) {
								scrollLockMeta = true;
								//  $('#complist .composite').css('opacity', '0.4');
								$('#overflow_middle .item:visible:eq(0)').animate({
									'margin-top' : '0px'
								}, function() {
									scrollLockCompMeta = false;
									//   $('#complist .composite').css('opacity', '1.0');
								});
							} else {
								scrollLockCompMeta = false;
							}
						});
					} else {
						scrollLockCompMeta = false;
					}
				}
				if($(oUpDown).hasClass('scroll_down')) {
					if(iCurScroll < (iSumHeight - $('#overflow_middle .scroll_panel').height() - iMetaVisibleHeight / 2)) {

						// $('#complist .composite').css('opacity', '0.4');
						$('#overflow_middle .item:visible:eq(0)').animate({
							'margin-top' : (((iMetaVisibleHeight / 2) * -1) - iCurScroll) + 'px'
						}, function() {
							scrollLockCompMeta = false;
							//  $('#complist .composite').css('opacity', '1.0');
						});
					} else {
						scrollLockCompMeta = false;
					}
				}
			}
			break;
	}
}