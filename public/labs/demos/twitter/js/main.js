// BOOTSTRAP VARIABLES (GLOBAL)
var sWho = 'demo';
var aTwitterRequests = {};
$(document).ready( function() {
	init();
	$('.tweet').live('mouseover', function() {
		$(this).animate({
			opacity:1
		}, 100);
	}).live('mouseout', function() {
		$(this).animate({
			opacity:0.7
		}, 500);
	});
});