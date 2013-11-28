function errorFunc(sError) {
	alert(sError);
}

function init() {
	$(document).askAPI({
		namespace:'composites',
		who:sWho,
		childsOf:'twitter',
		success:CompResponseHandler,
		error:errorFunc
	});
}

function CompResponseHandler(oComposites) {
	var iMaxTags = 10;
	var iRequests = 0;
	$.each(oComposites, function(iComposite, oComposite) {
		aTwitterRequests[iRequests] = {};
		aTwitterRequests[iRequests].hashtags = new Array();
		aTwitterRequests[iRequests].request = oComposite.source+'?q=';
		var bFirstRun = true;
		$.each(oComposite.tags.q.properties, function(sHastagNum, sHashTag) {
			if(sHashTag!==null) {
				aTwitterRequests[iRequests].hashtags[aTwitterRequests[iRequests].hashtags.length] = sHashTag.toLowerCase();
				if(bFirstRun) {
					aTwitterRequests[iRequests].request += sHashTag;
					bFirstRun = false;
				} else {
					aTwitterRequests[iRequests]['request'] += ','+sHashTag;
				}
			}
		});
		if(typeof(oComposite.tags.lang)!='undefined' && oComposite.tags.lang.properties.prefix!=null) {
			aTwitterRequests[iRequests]['request'] += '&lang='+oComposite.tags.lang.properties.prefix;
		}
		if(typeof(oComposite.tags.colorhex)!='undefined') {
			aTwitterRequests[iRequests]['color'] = oComposite.tags.colorhex.properties.text;
			aTwitterRequests[iRequests]['bordercolor'] = oComposite.tags.colorhex.properties.border;
			aTwitterRequests[iRequests]['background'] = oComposite.tags.colorhex.properties.background;
		}
		iRequests++;
	});
	callTwitter(aTwitterRequests);
}

function callTwitter(aTwitterRequest) {
	$.each(aTwitterRequest, function(iRequest, aRequestAndConf) {
		var sTplTweet = '<div class="tweet" style="border-color:#'+aRequestAndConf.bordercolor+';color:#'+aRequestAndConf.color+';background-color:#'+aRequestAndConf.background+'"><img src="{img}"/>FROM: {user}</br>MESSAGE: {message}</div>';
		$.getJSON(aRequestAndConf.request+'&rpp=1&callback=?', function(oData) {
			$.each(oData.results, function(iNumResult, oTweet) {
				
				var sTweetText = oTweet.text.toLowerCase();
				
				for(var i=0; i<aRequestAndConf.hashtags.length; i++){
					sTweetText = sTweetText.replace(aRequestAndConf.hashtags[i], '<span style="color:#ffff00;">'+aRequestAndConf.hashtags[i]+'<\/span>');
				} 
				
				var sDumpOut = sTplTweet.replace(/{img}/, oTweet.profile_image_url).replace(/{user}/, oTweet.from_user).replace(/{message}/, sTweetText);
				$('#demonstration').append(sDumpOut);
				sDumpOut = '';
			});
		});
	});
	$('#demonstration img').remove();
}
