function errorFunc(sError) {
	alert(sError);
}

function init() {
	/*$(document).askAPI({
	 namespace:'composites',
	 who:sWho,
	 success:writeDay,
	 error:errorFunc
	 });
	 */
	writeDay('frankfurt');
}

function writeDay(sWhere) {
	var sDayWeatherTpl = '<div class="day">';
	sDayWeatherTpl += '<img class="day_weather_img" src="{image}"/>';
	sDayWeatherTpl += '<div class="day_temp">';
	//sDayWeatherTpl += '13°';
	sDayWeatherTpl += '</div>';
	sDayWeatherTpl += '<div class="day_name">';
	sDayWeatherTpl += '{day}';
	sDayWeatherTpl += '</div>';
	sDayWeatherTpl += '<div class="day_info">';
	sDayWeatherTpl += '<span>{word}</span>';
	sDayWeatherTpl += '<span>Feuchtigkeit: 82 %</span>';
	sDayWeatherTpl += '<span>Wind: W mit 29 km/h</span>';
	sDayWeatherTpl += '</div>';
	sDayWeatherTpl += '</div>';

	$.get('getweather.php', {
		'in':sWhere
	}, function(oXML) {
		if($(oXML).find('forecast_conditions').length>0) {
			$(oXML).find('forecast_conditions').each( function(iNode, oNode) {
				var sDay = $(oXML).find('forecast_conditions:eq('+iNode+')').children('day_of_week').attr('data');
				var sIcon =$(oXML).find('forecast_conditions:eq('+iNode+')').children('icon').attr('data');
				var sWord =$(oXML).find('forecast_conditions:eq('+iNode+')').children('condition').attr('data');

				var sDumpOut = sDayWeatherTpl;
				sDumpOut = sDumpOut.replace(/{day}/, sDay);
				sDumpOut = sDumpOut.replace(/{image}/, sIcon);
				sDumpOut = sDumpOut.replace(/{word}/, sWord);
				$('#demonstration').append(sDumpOut);

				$('#loader').fadeOut(250);
				$('#location').css('border-color', '#ccc');
			});
		}else{
			$('#location').css('border-color', '#f00');
			$('#loader').fadeOut(250);
		}

	});
}