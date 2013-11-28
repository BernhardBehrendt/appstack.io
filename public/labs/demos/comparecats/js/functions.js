function successFunc(oData) {

	var sTable = '<table width="500">{tablerowcols}</table>'
	var sTableRow = '<tr><td>{name}</td><td>{comps}</td></tr>';
	var sDumpOut = '';
	var sTmp = '';

	$.each(oData, function(iComposite, oCategory) {

		sTmp += sTableRow.replace(/{name}/, '<b>'+oCategory.name+'</b>').replace(/{comps}/, oCategory.composites);

		iComposites+=oCategory.composites;

	});
	$('#content').append(sTable.replace(/{tablerowcols}/, sTmp));
	sTmp = '';
	
	drawChart(iComposites, oData);
}

function drawChart(iComposites, oCats) {
	var iPies = 0;
	var iSumVals = iComposites;
	
	var sChartVals = '&chd=t:';
	var bInitPies = true;
	var sChartDesc = '&chl=';

	$.each(oCats, function(iCat, oCat) {

		if(bInitPies) {
			sChartVals+=Math.round(((oCat.composites*100)/iSumVals));
			sChartDesc+=oCat.name;
			bInitPies = false;
		} else {
			sChartVals+=','+Math.round(((oCat.composites*100)/iSumVals));
			sChartDesc+='|'+oCat.name;
		}

	});
	var sUrlChart = 'http://chart.apis.google.com/chart?';
	var sChartKind = '&cht=p3';
	var sChartSize = '&chs=400x190';
	var sChartPies = '&chp='+iPies;
	var sChartBg = '&chf=bg,s,DDDDDD';

	var sChartColors = '&chco=000000,0000ff';
	//alert(sUrlChart+sChartKind+sChartSize+sChartPies+sChartVals+sChartDesc+sChartColors+sChartBg);
	$('#content').prepend('<img src="'+sUrlChart+sChartKind+sChartSize+sChartPies+sChartVals+sChartDesc+sChartColors+sChartBg+'"/>');
	bLockRequest = false;
}


function errorFunc(sError) {
	alert(sError);
	bLockRequest = false;
}

function visualize() {
	$(document).askAPI({
		namespace:'categories',
		who:sWho,
		success:successFunc,
		error:errorFunc
	});
}