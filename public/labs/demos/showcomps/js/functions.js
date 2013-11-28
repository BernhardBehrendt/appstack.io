function successFunc(oData) {

	var sTable = '<table width="500">{tablerowcols}</table>'
	var sTableRow = '<tr><td>{name}</td><td>{property}</td><td>{value}</td></tr>';
	var sDumpOut = '';
	var sTmp = '';

	$.each(oData, function(iComposite, oComposite) {
		$('body').append('<h2>'+oComposite.name+'</h2>');

		if(oComposite.category.name!=null) {
			$('body').append('<h3>Kategorie: '+oComposite.category.name+'</h3>');
		}

		$('body').append('<h4><a href="'+oComposite.source+'" target="_blank">'+oComposite.source+'</a></h4>');

		$.each(oComposite.tags, function(sTagname, oTag) {

			var sTmpFirst = sTableRow.replace(/{name}/, '<b>'+sTagname+'</b>');
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
		$('body').append(sTable.replace(/{tablerowcols}/, sTmp));
		sTmp = '';

	});
}

function errorFunc(sError) {
	alert(sError)
}