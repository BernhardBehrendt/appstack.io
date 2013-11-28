// BOOTSTRAP VARIABLES (GLOBAL)
var sWho = 'demo';
var aTwitterRequests = {};
$(document).ready( function() {
	init();
	$('.day img').live('mouseover', function() {
		$(this).parent().children('.day_info').fadeIn(250);
	}).live('mouseout', function() {
		$(this).parent().children('.day_info').fadeOut(250);
	});
	
	$('#location').live('keyup', function(evt){
		if(evt.keyCode==13){
			$('#loader').fadeIn(150);
			$('#demonstration').empty();
			writeDay($(this).val());
		}
	}).live('focusin', function(){
		$(this).val('');
	});
});