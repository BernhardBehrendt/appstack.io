function successFunc(oData) {

	var sTable = '<table width="500">{tablerowcols}</table>'
	var sTableRow = '<tr><td>{name}</td><td>{property}</td><td>{value}</td></tr>';
	var sDumpOut = '';
	var sTmp = '';

	$.each(oData, function(iComposite, oComposite) {
		$('#content').append('<h2>'+oComposite.name+'</h2>');

		if(oComposite.category.name!=null) {
			$('#content').append('<h3>Kategorie: '+oComposite.category.name+'</h3>');
		}

		$('#content').append('<h4><a href="'+oComposite.source+'" target="_blank">'+oComposite.source+'</a></h4>');
		$('#content').append('<strong>Created: '+oComposite.created+'</strong><br/>');
		$('#content').append('<strong>Modified: '+oComposite.modified+'</strong><br/>');
		$.each(oComposite.tags, function(sTagname, oTag) {

			var sTmpFirst = sTableRow.replace(/{name}/, '<b>'+sTagname+'</b>');
			oBinds[sTagname]++;
			var bFirst = true;

			$.each(oTag.properties, function(sPropName, mPropValue) {

				if(bFirst==true) {
					sTmp += sTmpFirst.replace(/{property}/, sPropName).replace(/{value}/, mPropValue);
					bFirst = false;
				} else {
					sTmp += sTableRow.replace(/{property}/, sPropName).replace(/{value}/, mPropValue).replace(/{name}/, '');;
				}

			});
		});
		$('#content').append(sTable.replace(/{tablerowcols}/, sTmp));
		sTmp = '';

	});
	drawChart(oBinds);
}

function drawChart(oStats) {
	var iPies = 0;
	var iSumVals = 0;
	$.each(oStats, function(sTagName, iAttached) {
		iPies++;
		iSumVals+=iAttached;
	});
	var sChartVals = '&chd=t:';
	var bInitPies = true;
	var sChartDesc = '&chl=';

	$.each(oStats, function(sTagName, iAttached) {

		if(bInitPies) {
			sChartVals+=Math.round(((iAttached*100)/iSumVals));
			sChartDesc+=sTagName;
			bInitPies = false;
		} else {
			sChartVals+=','+Math.round(((iAttached*100)/iSumVals));
			sChartDesc+='|'+sTagName;
		}

	});
	var sUrlChart = 'http://chart.apis.google.com/chart?';
	var sChartKind = '&cht=p3';
	var sChartSize = '&chs=400x190';
	var sChartPies = '&chp='+iPies;
	var sChartBg = '&chf=bg,s,DDDDDD';

	var sChartColors = '&chco=4800ff,f40b3d';
	//alert(sUrlChart+sChartKind+sChartSize+sChartPies+sChartVals+sChartDesc+sChartColors+sChartBg);
	$('#content').prepend('<img src="'+sUrlChart+sChartKind+sChartSize+sChartPies+sChartVals+sChartDesc+sChartColors+sChartBg+'"/>');
	bLockRequest = false;
}

function initStat(oData) {
	$.each(oData, function(iMeta, oMetas) {
		$.each(oMetas, function(sMetaName, oMeta) {
			oBinds[sMetaName] = 0;
		});
	});
	$(document).askAPI({
		namespace:'composites',
		who:sWho,
		success:successFunc,
		error:errorFunc
	});
}

function errorFunc(sError) {
	alert(sError);
	bLockRequest = false;
}

function visualize() {
	$(document).askAPI({
		namespace:'metas',
		who:sWho,
		success:initStat,
		error:errorFunc
	});
}