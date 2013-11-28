function errorFunc(sError) {
	alert(sError);
}

function init() {
	$(document).askAPI({
		namespace:'composites',
		who:sWho,
		category:'icons',
		success:CompResponseHandler,
		error:errorFunc
	});
}

function CompResponseHandler(oComposites) {
	$.each(oComposites, function(iComposite, oComposite) {
		$('body').append(oComposite.source+'<br/>');
		// Creates canvas 320 × 200 at 10, 50
		var paper = Raphael(10, 50, 320, 200);

		// Creates circle at x = 50, y = 40, with radius 10
		var circle = paper.path("223.992,45.997 249.683,98.052 307.129,106.4 265.561,146.919 275.374,204.134 223.992,177.121 172.61,204.134 182.423,146.919 140.854,106.4 198.301,98.052");
		// Sets the fill attribute of the circle to red (#f00)
	//	circle.attr("fill", "#f00");

		// Sets the stroke attribute of the circle to white
	//	circle.attr("stroke", "#fff");
	});
}