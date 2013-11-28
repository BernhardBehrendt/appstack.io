$(document).ready( function() {
	$(document).askAPI({
		namespace:'composites',
		who:'edinger',
		success:successFunc,
		error:errorFunc
	});

});
////////////// FUNKTIONEN ////////////////
