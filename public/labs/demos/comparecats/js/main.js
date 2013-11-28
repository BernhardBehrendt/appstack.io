var iComposites = 0;
var sWho = 'bernhardb';
var bLockRequest = false;
$(document).ready( function() {
	$('#who').val(sWho);
	visualize();

	$('#doVis').click( function() {
		if(!bLockRequest) {
			oBinds = {};
			$('#content').empty();
			sWho = $('#who').val();
			visualize();
			bLockRequest = true;
		}
		return false;
	});
	$('#who').keyup( function(e) {

		if(e.keyCode==13) {
			if(!bLockRequest) {
				oBinds = {};
				$('#content').empty();
				sWho = $('#who').val();
				visualize();
			}
		}
		return false;
	}).focusin( function() {
		$(this).val('');
	});
});
////////////// FUNKTIONEN ////////////////