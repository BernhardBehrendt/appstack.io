// VARS
var sFocusinTmp = false;
var iSumWidth = $('.wideimage').length * 953;
var bLock = false;
var mTmp = false;
var iLastAccID = false;
var iLastGroupID = false;
var sLastGroupPanel = false;
$(document).ready(function() {

	addEventListener("load", function() {
		setTimeout(hideURLbar, 0);
	}, false);
	function hideURLbar() {
		window.scrollTo(0, 1);
	}

	if ($('#wide_slide_slider').find('img').length > 1) {
		autoslide('#wide_slide_slider', 'a', 953, 6000, 1200);
	}
	$(document).on('focusin', "#interact input", function() {
		sFocusinTmp = $(this).val();
		$(this).val('');
	}).on('focusout', function() {
		if ($(this).val() == '') {
			$(this).val(sFocusinTmp);
		}
	});

	$("#tabs").tabs();
	$.each($('tspan'), function(i, oTspan) {
		if ($(this).text().match(/:/)) {
			$(this).text('');
		}
	});
}).on('click', '#account', function() {
	if (!$(this).hasClass('active')) {
		$(this).addClass('active');
		$.getJSON('account/status', function(oResponse) {
			if (!oResponse.error) {
				$('#interact form').hide();
				if ($('#my-profile').length != 0) {
					$('#my-profile').remove();
				}
				$('#interact').prepend(oResponse.widget);
				$('#my-profile').delay(oResponse.expires).fadeOut(250, function() {
					$('#account').css('color', '#ffffff');
					$(this).remove();
					$('#search_form').show();
				});
			} else {
				$('#interact form').toggle();
			}
		});
	} else {
		if ($('#my-profile').length == 0) {
			$(this).removeClass('active');
			$('#interact form').toggle();
		}

	}
	return false;
}).on('click', '#nopw', function() {
	if ($(this).text() == 'Forgot password') {
		alert('d');
		var sUsername = $('input[name="username"]').val();
		if (sUsername.length > 5 && sUsername.length < 31) {
			alert('e');
			$.get('account/nopw/', {
				'username' : $('input[name="username"]').val()
			}).success(function(oResponse) {
				alert('f');
				$('#nopw').text(oResponse).addClass('apst_cursor_none');
			});
		}
	}
	return false;
}).on('click', '#login', function() {
	$('#login_form').submit();
	return false;
}).on('keydown', '#login_input input', function(e) {
	if (e.keyCode == 13) {
		$('#login_form').submit();
		return false;
	}
}).on('focusin', '#login_input input', function() {
	$('#register').hide();
	$('#nopw').show();
}).on('keyup', '#user', function() {
	if ($(this).val().length > 5 && $(this).val().length < 21) {
		var oThis = $(this);
		$.getJSON('account/lookup', {
			'user' : $(this).val()
		}, function(oResponse) {
			if (!oResponse.error) {
				$(oThis).removeClass('apst_red');
				$(oThis).addClass('apst_green');
			} else {
				$(oThis).removeClass('apst_green');
				$(oThis).addClass('apst_red');
			}
		});
	} else {
		$(this).removeClass('apst_red');
		$(this).removeClass('apst_green');
	}
}).on('keyup', '#pwa, #pwb', function() {
	if ($('#pwa').val() == $('#pwb').val()) {
		$('#pwa, #pwb').addClass('apst_green');
	} else {
		$('#pwa, #pwb').removeClass('apst_green');
	}
}).on('keyup', '#maila, #mailb', function() {
	if ($('#maila').val() == $('#mailb').val()) {
		$('#maila, #mailb').addClass('apst_green');
	} else {
		$('#maila, #mailb').removeClass('apst_green');
	}
}).on('click', '.transfer a', function() {
	/**
	 *	SUBACCOUNTS HANDLING STARTS HERE
	 */
	var iAccID = $(this).parent().parent().attr('id');
	if (iAccID != iLastAccID) {
		iLastAccID = iAccID;
		var iFinalHeight = 77;
		$.get('subaccount/transfermask', {
			account : iAccID
		}, function(data) {
			if ($('#transfermask').length > 0) {

				$('#transfermask').animate({
					height : 0
				}, 500, function() {
					$(this).remove();
					$('#' + iAccID).after(data);
					if ($('#' + iAccID).hasClass('zeb')) {
						$('#transfermask').addClass('zeb');
						var sColor = '#333';
					} else {
						$('#transfermask').addClass('ra');
						var sColor = '#222';
					}
					if ($('.fadable').length > 0) {

						$('#transfermask').animate({
							height : $('.fadable').length * iFinalHeight + 35
						}, 500, function() {
							bPlugInit = false;
							$('.fadable').fadable();
						});
					} else {
						$('#transfermask').remove();
						$('#' + iAccID).animate({
							'background-color' : '#ed0178'
						}, 250, function() {
							$('#' + iAccID).animate({
								'background-color' : sColor
							}, 250);
							iLastAccID = false;
						});
					}
				});
			} else {
				$('#' + iAccID).after(data);

				if ($('#' + iAccID).hasClass('zeb')) {
					$('#transfermask').addClass('zeb');
					var sColor = '#333';
				} else {
					$('#transfermask').addClass('ra');
					var sColor = '#222';
				}
				if ($('.fadable').length > 0) {
					$('#transfermask').animate({
						height : $('.fadable').length * iFinalHeight + 35
					}, 500, function() {
						bPlugInit = false;
						$('.fadable').fadable();
					});
				} else {
					$('#transfermask').remove();
					$('#' + iAccID).animate({
						'background-color' : '#ed0178'
					}, 250, function() {
						$(this).animate({
							'background-color' : sColor
						}, 250);
						iLastAccID = false;
					});
				}
			}
		});
	}
	return false;
}).on('click', '#close-view', function() {
	$('#transfermask').animate({
		height : 0
	}, 500, function() {
		$(this).remove();
		iLastAccID = false;
	});
	return false;
}).on('click', '#transfer-budget', function() {

	var oTransferData = {};
	var oAccountrates = $(this).parent().parent().children('.fadable');
	oTransferData.account = $(this).parent().parent().parent().prev().attr('id');
	var bValidData = true;
	$.each($(oAccountrates), function() {

		var iSummary = parseInt($(this).find('.summary').val());
		var iMine = parseInt($(this).find('.mine').val());
		var iHis = parseInt($(this).find('.his').val());
		if ((iMine + iHis) === iSummary) {
			var oHisMine = {};
			oHisMine.his = iHis;
			oHisMine.mine = iMine;
			oTransferData[$(this).attr('id').replace(/HAS/g, '')] = oHisMine;
		} else {
			bValidData = false;
			$('#transfermask').animate({
				height : 0
			}, 500, function() {
				$(this).remove();
				iLastAccID = false;
			});
		}
	});
	if (bValidData) {
		$.getJSON($('base').attr('href') + "subaccount/transferdo", oTransferData).success(function(data) {
			if ( typeof (data.error) === 'undefined') {
				var sUpdColor = '#ed0178';
				if ( typeof (data.success) === 'boolean') {
					var sUpdColor = '#062ca0';
				}
				$('#' + oTransferData.account).animate({
					backgroundColor : sUpdColor
				}, 500);

				$('#transfermask').animate({
					height : 0
				}, 500, function() {
					$(this).remove();
					iLastAccID = false;
					if ($('#' + oTransferData.account).hasClass('zeb')) {
						var sColor = '#333';
					}
					if ($('#' + oTransferData.account).hasClass('ra')) {
						var sColor = '#222';
					}
					$('#' + oTransferData.account).animate({
						backgroundColor : sColor
					}, 500);
				});
			} else {
				alert(data.error);
			}
		});
	}
	return false;
}).on('keyup', '#namespace', function(e) {
	/**
	 *	NAMESPACES HANDLING STARTS HERE
	 */
	$(this).val($(this).val().toLowerCase().replace(/ /g, ''));

	if ($('#createnamespace:visible').length > 0) {
		if ($(this).val().length <= 3) {
			$('#createnamespace').fadeOut(500);
			$(this).removeClass('apst_green');
		}
	}

	if ($(this).val().length > 3 && $(this).val().length <= 50 && e.keyCode != 13) {
		$.getJSON('namespaces/lookup/', {
			'namespace' : $(this).val()
		}).success(function(data) {
			if ($('#namespaceloader:visible').length > 0) {
				$('#namespaceloader').fadeOut(50);
			}
			if (!data.error) {
				$('#createnamespace').fadeIn(100);
				$('#namespace').addClass('apst_green');
				$('#namespace, #namespacecreate label').removeClass('apst_pink');
			} else {
				$('#createnamespace').fadeOut(100);
				$('#namespace').removeClass('apst_green');
				$('#namespace, #namespacecreate label').addClass('apst_pink');
			}

			$('#namespacecreate').children('label').text(data.message).css('display', 'inline').delay(3000).fadeOut(500);
		}).error(function() {

		});
	} else {
		$('#namespacecreate').children('label').text('');
	}
}).on('click', '#createnamespace', function() {
	$('#namespacecreate').children('label').text('');
	$('#namespacecreate').submit();
	return false;
}).on('submit', '#namespacecreate', function() {
	if ($('#createnamespace:visible').length > 0 && $('#namespace').hasClass('apst_green')) {
		$('#createnamespace').fadeOut(100, function() {
			$('#namespaceloader').fadeIn(100);
			$.getJSON($('#namespacecreate').attr('action'), $('#namespacecreate').serialize()).success(function(data) {
				$('#namespaceloader').fadeOut(500, function() {
					if (!data.error) {

						var oIndexPage = {};

						$('#namespacecreate').children('label').text(data.message).css('display', 'inline').delay(6000).fadeOut(500);
						$('#namespacecreate').children('label').removeClass('apst_green').removeClass('apst_pink');
						if ($('.pages:visible').length > 0) {
							$.each($('.pages:visible'), function(i, oLimiter) {
								if ($(this).hasClass('apst_pink')) {
									oIndexPage.list_index = parseInt($(this).attr('href').split('list_index=')[1]);
								}
							});
						}

						$.get('namespaces/list', oIndexPage).success(function(data) {
							$('.pages').remove();
							$('.table').replaceWith(data);
						});

						$('#namespace').val('').blur();
					} else {
						$('#namespacecreate').children('label').text(data.message).css('display', 'inline').delay(6000).fadeOut(500);
						$('#namespacecreate').children('label').removeClass('apst_green').addClass('apst_pink');
						$('#namespace').addClass('apst_pink').removeClass('apst_green');
					}
				});
			}).error(function() {
				$('#namespaceloader').fadeOut(500);
			});
		});
	}
	$('#namespace').removeClass('apst_green');
	return false;
}).on('click', '.nsdelete', function(event) {
	if (!bLock) {
		var oNsRow = $(this).parent().parent();
		bLock = true;
		$.getJSON('namespaces/delete', {
			'idns' : oNsRow.children('.id').text()
		}).success(function(oResponse) {
			if (!oResponse.error) {
				oNsRow.fadeOut(500, function() {
					$(this).remove();
					var oIndexPage = {};

					if ($('.pages:visible').length > 0) {
						$.each($('.pages:visible'), function(i, oLimiter) {
							if ($(this).hasClass('apst_pink')) {
								oIndexPage.list_index = parseInt($(this).attr('href').split('list_index=')[1]);
							}
						});
					}

					$.get('namespaces/list', oIndexPage).success(function(data) {
						$('.pages').remove();
						$('.table').replaceWith(data);
						bLock = false;
					});
				});
			} else {
				alert(oResponse.error);
				bLock = false;
			}
		});
	}
	return false;
}).on('keyup', '#group', function(e) {
	/**
	 *	GROUP HANDLING STARTS HERE
	 */
	if ($('#creategroup:visible').length > 0) {
		if ($(this).val().length <= 3) {
			$('#creategroup').fadeOut(500);
			$(this).removeClass('apst_green');
		}
	}

	$(this).val($(this).val().toLowerCase().replace(/ /g, ''));

	if ($(this).val().length > 3 && $(this).val().length <= 50 && e.keyCode != 13) {
		$.getJSON('groups/lookup/', {
			'group' : $(this).val()
		}).success(function(data) {
			if ($('#grouploader:visible').length > 0) {
				$('#grouploader').fadeOut(50);
			}
			if (!data.error) {
				$('#creategroup').fadeIn(100);
				$('#group').addClass('apst_green');
				$('#group, #groupcreate label').removeClass('apst_pink');
			} else {
				$('#creategroup').fadeOut(100);
				$('#group').removeClass('apst_green');
				$('#group, #groupcreate label').addClass('apst_pink');
			}

			$('#groupcreate').children('label').text(data.message).css('display', 'inline').delay(3000).fadeOut(500);
		}).error(function() {

		});
	} else {
		$('#groupcreate').children('label').text('');
	}
}).on('click', '#creategroup', function() {
	$('#groupcreate').children('label').text('');
	$('#groupcreate').submit();
	return false;
}).on('submit', '#groupcreate', function() {
	iLastGroupID = false;
	if ($('#creategroup:visible').length > 0 && $('#group').hasClass('apst_green')) {
		$('#creategroup').fadeOut(100, function() {
			$('#grouploader').fadeIn(100);
			$.getJSON($('#groupcreate').attr('action'), $('#groupcreate').serialize()).success(function(data) {
				$('#grouploader').fadeOut(500, function() {
					if (!data.error) {

						var oIndexPage = {};

						$('#groupcreate').children('label').text(data.message).css('display', 'inline').delay(6000).fadeOut(500);
						$('#groupcreate').children('label').removeClass('apst_green').removeClass('apst_pink');
						if ($('.pages:visible').length > 0) {
							$.each($('.pages:visible'), function(i, oLimiter) {
								if ($(this).hasClass('apst_pink')) {
									oIndexPage.list_index = parseInt($(this).attr('href').split('list_index=')[1]);
								}
							});
						}

						$.get('groups/list', oIndexPage).success(function(data) {
							$('.pages').remove();
							$('.table').replaceWith(data);
						});

						$('#group').val('').blur();
					} else {
						$('#groupcreate').children('label').text(data.message).css('display', 'inline').delay(6000).fadeOut(500);
						$('#groupcreate').children('label').removeClass('apst_green').addClass('apst_pink');
						$('#group').addClass('apst_pink').removeClass('apst_green');
					}
				});
			}).error(function() {
				$('#grouploader').fadeOut(500);
			});
		});
	}
	$('#group').removeClass('apst_green');
	return false;
}).on('click', '.grdelete', function(event) {
	if (!bLock) {
		var oNsRow = $(this).parent().parent();
		bLock = true;
		$.getJSON('groups/delete', {
			'idg' : oNsRow.children('.id').text()
		}).success(function(oResponse) {
			if (!oResponse.error) {
				oNsRow.fadeOut(500, function() {
					$(this).remove();
					var oIndexPage = {};

					if ($('.pages:visible').length > 0) {
						$.each($('.pages:visible'), function(i, oLimiter) {
							if ($(this).hasClass('apst_pink')) {
								oIndexPage.list_index = parseInt($(this).attr('href').split('list_index=')[1]);
							}
						});
					}

					$.get('groups/list', oIndexPage).success(function(data) {
						$('.pages').remove();
						$('.table').replaceWith(data);
						bLock = false;
					});
				});
			} else {
				alert(oResponse.error);
				bLock = false;
			}
		});
	}
	return false;
}).on('click', '.grnsassign, .grusrassign', function() {

	if (!bLock) {
		var sGetRessource = 'groups/addspaces';
		var iGroupID = $(this).parent().parent().attr('id');

		if (iGroupID != iLastGroupID || sLastGroupPanel != $(this).attr('class')) {
			iLastGroupID = iGroupID;
			sLastGroupPanel = $(this).attr('class');
			bLock = !bLock;
			if ($(this).hasClass('grusrassign')) {
				sGetRessource = 'groups/addusers';
			}
			$.get(sGetRessource, {
				groupid : iGroupID
			}).success(function(data) {
				if ($('#grpconfig').length > 0) {

					$('#grpconfig').animate({
						height : 0
					}, 500, function() {
						$(this).remove();
						$('#' + iGroupID).after(data);
						if ($('#' + iGroupID).hasClass('zeb')) {
							$('#grpconfig').addClass('zeb');
							var sColor = '#333';
						} else {
							$('#grpconfig').addClass('ra');
							var sColor = '#222';
						}

						$('#grpconfig').animate({
							height : 330
						}, 500, function() {
							var iPages = Math.ceil(($('.rowset').length / 10));
							$('#paging').html(Array(iPages + 1).join($('#paging').html())).fadeIn(250);
							$('.slidens:eq(0), .slideusr:eq(0)').addClass('active');
							if ($('.slidens, .slideusr').length == 1) {
								$('.slidens, .slideusr').remove();
							}
							// SETUP HERE
							bLock = !bLock;
						});
					});
				} else {

					$('#' + iGroupID).after(data);

					if ($('#' + iGroupID).hasClass('zeb')) {
						$('#grpconfig').addClass('zeb');
						var sColor = '#333';
					} else {
						$('#grpconfig').addClass('ra');
						var sColor = '#222';
					}
					$('#grpconfig').animate({
						height : 330
					}, 500, function() {
						var iPages = Math.ceil(($('.rowset').length / 10));
						$('#paging').html(Array(iPages + 1).join($('#paging').html())).fadeIn(250);
						$('.slidens:eq(0), .slideusr:eq(0)').addClass('active');
						// SETUP HERE
						if ($('.slidens, #slideusr').length == 1) {
							$('#grprights').css('margin-top', '22px');
							$('#paging').remove();
						}
						bLock = !bLock;
					});
				}
			});
		}
	}
	return false;
}).on('click', '.slidens, slidegrp', function() {
	if (!$(this).hasClass('active')) {

		var iAnimateTime = 500 * ($(this).index() - $('.active').index());
		iAnimateTime = (parseInt(iAnimateTime) < 0) ? (-1 * iAnimateTime) : iAnimateTime;
		$('.slidens, slidegrp').removeClass('active');
		$(this).addClass('active');
		$('#nslist, #usrlist').css('opacity', '0.5');

		$('#nslist .rowset:visible:eq(0), #usrlist .rowset:eq(0)').animate({
			'margin-top' : (-1 * ($(this).index() * ($('#nslist, #usrlist').height())))
		}, iAnimateTime, function() {
			$('#nslist, #usrlist').animate({
				'opacity' : 1
			}, 300);
		});
	}
	return false;
}).on('click', '.configure', function() {
	$.get('groups/nscats/', {
		'idns' : $(this).parent().attr('id')
	}).success(function(data) {
		$('#catconf').html(data);
		var iMrgLeft = ((($('#grpconfig').width() / 2) + 12) * -1);
		$('#nsconf, #paging, #searchspace').animate({
			'margin-left' : iMrgLeft
		}, 1000);
	});
	return false;
}).on('click', '.nsback', function() {
	$('#nsconf, #paging, #searchspace').animate({
		'margin-left' : '0px'
	}, 1000, function() {
		$('#treeview, #modeselect').remove();
	});
	return false;
}).on('click', '#nscattree a', function() {
	if ($(this).hasClass('jstree-multi')) {
		$(this).removeClass('jstree-multi');
	} else {
		$(this).addClass('jstree-multi');
	}
}).on('change', 'input[name="mode_select"]', function() {
	$.each($('#treeview a'), function() {
		if ($(this).hasClass('jstree-multi')) {
			$(this).removeClass('jstree-multi');
		} else {
			$(this).addClass('jstree-multi');
		}
	});
}).on('click', '.bind', function() {
	if (!bLock) {
		bLock = true;
		var iNsId = parseInt($(this).parent().attr('id').replace(/ns_/, ''));
		var oThis = $(this);
		$.getJSON('groups/bind/', {
			'idns' : iNsId,
			'idgroup' : iLastGroupID
		}).success(function(oResponse) {
			if (!oResponse.error) {
				$('#ns_' + iNsId).children('.configure').fadeIn();
				$(oThis).removeClass('bind').addClass('unbind');
				bLock = false;
			}
		}).error(function(oResponse) {
			//	window.location.reload();
			bLock = false;
		});
	}
	return false;
}).on('click', '.unbind', function() {
	if (!bLock) {
		bLock = true;
		var iNsId = parseInt($(this).parent().attr('id').replace(/ns_/, ''));
		var oThis = $(this);
		$.getJSON('groups/unbind/', {
			'idns' : iNsId,
			'idgroup' : iLastGroupID
		}).success(function(oResponse) {
			if (!oResponse.error) {
				$('#ns_' + iNsId).children('.configure').fadeOut();
				$(oThis).removeClass('unbind').addClass('bind');
				bLock = false;
			}
		}).error(function(oResponse) {
			//	window.location.reload();
			bLock = false;
		});
	}
	return false;
}).on('click', '.member', function() {
	if (!bLock) {
		bLock = true;
		var iUsrId = parseInt($(this).parent().attr('id').replace(/usr_/, ''));
		var oThis = $(this);
		$.getJSON('groups/member/', {
			'idusr' : iUsrId,
			'idgroup' : iLastGroupID
		}).success(function(oResponse) {
			if (!oResponse.error) {
				$(oThis).removeClass('member').addClass('nomember');
				bLock = false;
			}
		}).error(function(oResponse) {
			//	window.location.reload();
			bLock = false;
		});
	}
	return false;
}).on('click', '.nomember', function() {
	if (!bLock) {
		bLock = true;
		var iUsrId = parseInt($(this).parent().attr('id').replace(/usr_/, ''));
		var oThis = $(this);
		$.getJSON('groups/nomember/', {
			'idusr' : iUsrId,
			'idgroup' : iLastGroupID
		}).success(function(oResponse) {
			if (!oResponse.error) {
				$(oThis).removeClass('nomember').addClass('member');
				bLock = false;
			}
		}).error(function(oResponse) {
			//	window.location.reload();
			bLock = false;
		});
	}
	return false;
}).on('change', '#grprights input[type="checkbox"]', function() {
	var oSetRight = {};
	oSetRight[$(this).attr('name')] = ($(this).is(':checked')) ? 1 : 0;

	$.getJSON('groups/setright', {
		'groupid' : iLastGroupID,
		'setright' : oSetRight
	}).success(function(oResponse) {
		if (oResponse.error) {
			alert(oResponse.message);
		}
	});
}).on('submit', '#searchspace', function() {
	return false;
}).on('focusin', '#searchexpr', function() {
	if ($('#paging .slidens').length > 1 && !$('#paging .slidens:eq(0)').hasClass('active')) {
		$('#paging .slidens:eq(0)').trigger('click');
	}
	$('.rowset').show();
	$(this).val('');
	createPaging();
}).on('focusout', function() {
	$(this).val('')
}).on('keyup', function(e) {
	if (e.keyCode == 13) {
		if ($('.rowset:visible').length == 0) {
			$('.rowset').show();
			createPaging();
		}
		$(this).val();
		$(this).blur();
	} else {
		var sExpr = $(this).val().toLowerCase();
		$.each($('.rowset'), function(i, oRow) {
			var sRowName = $(this).children('.name').text().toLowerCase();
			if (sRowName.search(sExpr) == -1) {
				$(this).hide()
			} else {
				$(this).show();
			}
		});
		createPaging();
	}

}).on('click', '.nsgrpopen', function() {

	if (!$(this).hasClass('active')) {
		$('.nsgroup').children('.active').trigger('click');
		var oGrpNs = $(this).parent();
		var iEndHeight = (oGrpNs.children('.row').length - 1) * 40 + (26 + 25);
		oGrpNs.animate({
			'height' : iEndHeight
		}, function() {
			oGrpNs.children('.nsgrpopen').addClass('active');
		});
	} else {
		$(this).parent().animate({
			'height' : '25px'
		}, 500, function() {
			$(this).children('.nsgrpopen').removeClass('active');
		});
	}
	return false;
}).on('click', '.nsopen', function() {
	var oThis = $(this);
	var iIdNs = $(this).parent().parent().attr('id');
	window.open('builder/run/?idns=' + iIdNs);
	return false;
}).on('click', '.close_frame', function() {
	$('iframe').fadeOut(500, function() {
		$(this).remove();
	});
	$('#footer, #wrapper').fadeIn(500, function() {
		$('.close_frame').fadeOut(500, function() {
			$(this).remove()
		});
	});
	return false;
}).on('click', '.skview, .bkview', function() {
	// SERVERKEYS START HERE

	if ($(this).parent().parent().next().hasClass('dn')) {
		$('.accesstokens:not(.dn),.browserkey:not(.dn)').addClass('dn').removeClass('clearfix');
		$(this).parent().parent().next().removeClass('dn').addClass('clearfix');
	} else {
		$(this).parent().parent().next().addClass('dn').removeClass('clearfix');
	}
	return false;
}).on('click', '.acctkdel', function() {
	var oRemove = $(this).parent().parent();
	$.getJSON('apis/keydelete', {
		'key' : $(this).prev().text()
	}).success(function(oResponse) {
		if (!oResponse.error) {
			oRemove.fadeOut(250, function() {
				var oKeySum = $(this).parent().prev().children('.created');
				oKeySum.text(parseInt(oKeySum.text()) - 1);
				$(this).remove();
			})
		} else {
			alert(oResponse.message);
		}
	});
	return false;
}).on('click', '.skdelete', function() {
	var oRemove = $(this).parent().parent();
	var oNext = oRemove.next();
	$.getJSON('apis/consumerdelete', {
		'consumer' : oRemove.attr('id')
	}).success(function(oResponse) {
		if (!oResponse.error) {
			oRemove.fadeOut(250, function() {
				oRemove.remove();
				oNext.remove();
				if ($('.accesstokens').length == 0) {
					$.get(window.location.href).success(function(oResponse) {
						var iFrom = parseInt(oResponse.search(/<body class="apst_font_fam">/)) + 28;
						var iTo = oResponse.search(/<\/body>/);

						$('body').html(oResponse.substring(iFrom, iTo));
					});
				}
			})
		} else {
			alert(oResponse.message);
		}
	});
	return false;
}).on('click', '.consumercreateopen', function() {
	if ($(this).hasClass('active')) {
		$('input').blur();
		$(this).removeClass('active');
		$(this).parent().parent().animate({
			'height' : '20px'
		}, 500);
	} else {
		$(this).addClass('active');
		$(this).parent().parent().animate({
			'height' : '208px'
		}, 500, function() {
			$('#admin').focus();
		});
	}
	return false;
}).on('keyup', '#consumercreate input, #browsercreate input', function(e) {

	if (e.keyCode == 13) {
		$(this).blur();
	}
}).on('blur', '#consumercreate input', function() {
	var sInp = $(this).attr('id');
	switch(sInp) {
		case 'admin':
			$('#appmail').focus();

			break;
		case 'appmail':
			if (isValidEmail($(this).val())) {
				$('#appurl').focus();
				$(this).removeClass('apst_pink');
			} else {
				if ($(this).val().length > 0) {
					$(this).addClass('apst_pink');
				}
			}
			break;
		case 'appurl':
			if (isValidURL($(this).val())) {
				$('#callback').focus();
				$(this).removeClass('apst_pink');
			} else {
				if ($(this).val().length > 0) {
					$(this).addClass('apst_pink');
				}

			}
			break;
		case 'callback':
			if (isValidURL($(this).val())) {
				$(this).removeClass('apst_pink');
			} else {
				if ($(this).val().length > 0) {
					$(this).addClass('apst_pink');
				}
			}
			break;
	}
	if ($('#admin').val().length > 0 && $('#appmail').val().length > 0 && $('#appurl').val().length > 0 && $('#callback').val().length > 0) {
		if ($('#consumercreatemask input.apst_pink').length == 0) {
			$('#createconsumer').show().focus();
		} else {
			$('#createconsumer').hide().blur();
		}
	}

}).on('click', '#createconsumer', function() {
	$('#serverkeyloader').show();
	$(this).hide();
	var oForm = $('#consumercreate').serialize();

	$.getJSON($('#consumercreate').attr('action'), oForm).success(function(oResponse) {
		$('#serverkeyloader').hide();
		if (!oResponse.error) {
			$('#consumercreatemask').animate({
				'height' : '20px'
			}, 500, function() {
				$.get(window.location.href).success(function(oResponse) {
					var iFrom = parseInt(oResponse.search(/<body class="apst_font_fam">/)) + 28;
					var iTo = oResponse.search(/<\/body>/);
					$('body').html(oResponse.substring(iFrom, iTo));
				});
			});
		} else {
			alert(oResponse.message);
		}
	});
	return false;
}).on('keydown', '#createconsumer', function(e) {
	return false;
}).on('submit', '#consumercreate, #browsercreate', function() {
	return false;
}).on('click', '.browserkescreateopen', function() {
	if ($(this).hasClass('active')) {
		$('input').blur();
		$(this).removeClass('active');
		$(this).parent().parent().animate({
			'height' : '20px'
		}, 500);
	} else {
		$(this).addClass('active');
		$(this).parent().parent().animate({
			'height' : '208px'
		}, 500, function() {
			$('#appurl').focus();
		});
	}
	return false;
}).on('change', '#browsercreate input', function() {
	if (isValidURL('http://' + $('#appurl').val().replace('http://', '')) || $('#anywhere:checked').length == 1) {
		$('#createbrowserkey').show();
		$('#appurl').removeClass('apst_pink');
	} else {
		$('#createbrowserkey').hide();
		if ($('#appurl').val().length > 0 && isValidURL('http://' + $('#appurl').val().replace('http://', ''))) {
			$('#appurl').addClass('apst_pink');
		}
	}
	return false;
}).on('click', '#createbrowserkey', function() {
	$('#browserkeyloader').show();
	$.getJSON($('#browsercreate').attr('action'), $('#browsercreate').serialize()).success(function(oResponse) {
		$('#browserkeyloader').hide();
		if (!oResponse.error) {
			$('#browserkeycreatemask').animate({
				'height' : '20px'
			}, 500, function() {
				$.get(window.location.href).success(function(oResponse) {
					var iFrom = parseInt(oResponse.search(/<body class="apst_font_fam">/)) + 28;
					var iTo = oResponse.search(/<\/body>/);
					$('body').html(oResponse.substring(iFrom, iTo));
				});
			});
		} else {
			alert(oResponse.message);
		}
	});
	return false;
}).on('click', '.bkdelete', function() {
	var oRemove = $(this).parent().parent();
	var oNext = oRemove.next();
	$.getJSON('apis/browserkeydelete', {
		'browserkey' : oRemove.attr('id')
	}).success(function(oResponse) {
		if (!oResponse.error) {
			oRemove.fadeOut(250, function() {
				oRemove.remove();
				oNext.remove();
				if ($('.accesstokens').length == 0) {
					$.get(window.location.href).success(function(oResponse) {
						var iFrom = parseInt(oResponse.search(/<body class="apst_font_fam">/)) + 28;
						var iTo = oResponse.search(/<\/body>/);

						$('body').html(oResponse.substring(iFrom, iTo));
					});
				}
			})
		} else {
			alert(oResponse.message);
		}
	});
	return false;
});
function isValidURL(url) {
	var RegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

	if (RegExp.test(url)) {
		return true;
	} else {
		return false;
	}
}

function isValidEmail(mail) {
	var RegExp = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;
	if (RegExp.test(mail)) {
		return true;
	} else {
		return false;
	}
}

function createPaging() {
	$('.rowset').removeClass('zeb ra');
	var sZebraInit = 'zeb';
	$.each($('.rowset:visible'), function(i, oRowset) {
		$(this).addClass(sZebraInit);
		if (sZebraInit == 'zeb') {
			sZebraInit = 'ra';
		} else {
			sZebraInit = 'zeb';
		}
	});
	var iPages = Math.ceil((($('.rowset:visible').length + 1) / 10));
	$('#paging').html(Array(iPages + 1).join('<a href="#" class="slidens fl_left"></a>')).fadeIn(250);
	$('.slidens:eq(0), .slideusr:eq(0)').addClass('active');
	if ($('.slidens, .slideusr').length == 1) {
		$('.slidens, .slideusr').remove();
	}
}

function autoslide(oSlider, sElement, iUnit, iDelay, iDuration) {
	var iCurMargin = parseInt($(oSlider).css('margin-left').replace(/px/, ''));
	iCurMargin = (iCurMargin < 0) ? iCurMargin * -1 : iCurMargin;

	var iMaxSlide = $(oSlider).children(sElement).length * iUnit;

	$(oSlider).width(iMaxSlide);

	if ((iCurMargin + iUnit) < iMaxSlide) {
		$(oSlider).delay(iDelay).animate({
			'margin-left' : '-' + (iCurMargin + iUnit) + 'px'
		}, iDuration, function() {
			autoslide(oSlider, sElement, iUnit, iDelay, iDuration);
		});
	} else {
		$(oSlider).delay(iDelay).animate({
			'opacity' : '0'
		}, 250, function() {
			$(oSlider).css('margin-left', '0px');
			$(oSlider).animate({
				'opacity' : '1'
			}, 250, function() {
				autoslide(oSlider, sElement, iUnit, iDelay, iDuration);
			});
		});
	}
}