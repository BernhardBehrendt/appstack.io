/**
 * Controll Messagelayer (Icon / Headline / Text)
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 * @param {String} type
 * @param {String} headline
 * @param {String} text
 * @param {String} greenlabel
 * @param {String} redlabel
 * @param {Bool} update
 *
 */
function openDialog(type, headline, text, greenlabel, redlabel, update, abortable){
	$("body").css("overflow-y", "auto");
    $('#confirm').css('height', (parseInt($(document).height()) - 3) + 'px');
    if (sRequestSender == 'close') {
        changeConfirmationIcon('../../img/' + type + '.png');
        changeConfirmationMain($("#confirm #message #message_textarea #message_text").html() + '<br/><p><hr/></p><br/><h2>' + headline + '</h2>' + text);
        $('#confirm').css('height', (parseInt($(document).height()) - 3) + 'px');
        return false;
    }
    if (update) {
        changeConfirmationIcon('../../img/' + type + '.png');
        changeConfirmationHead(headline);
        changeConfirmationMain(text);
        changeConfirmationBtnRed(redlabel);
        changeConfirmationBtnGreen(greenlabel);



    }
    else {
        //$("#abort").css('display', 'none');
    }
    window.scrollTo(0, 0);
    $("#confirm").fadeIn(250, function(){
        resetConfirmationBtnGreen();
        if (abortable === true) {
            showConfirmationBtnRed();
        }else{
			hideConfirmationBtnRed();
		}
        $('#confirm').css('height', (parseInt($(document).height()) - 4) + 'px');

    });
}

/**
 * Shows default loader
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {Object} target
 */
function showConfirmationLoader(){
    $('#confirmed').css('display', 'none');
    $('.msg_window_loader').fadeIn(250);
}

/**
 * Changes confirmation headline
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {String} sHeadline
 */
function changeConfirmationHead(sHeadline){
    $("#confirm #message #message_textarea #message_headline h1").text(sHeadline);
}

/**
 * Changes confirmation main
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {String} sHtml
 */
function changeConfirmationMain(sHtml){
    $("#confirm #message #message_textarea #message_text").html(sHtml);
}

/**
 * Changes confirmation message icon
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {String} sImagePath
 */
function changeConfirmationIcon(sImagePath){
    $("#confirm #message #message_type").attr('src', sImagePath);
}

/**
 * Changes red button text
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {String} sText
 */
function changeConfirmationBtnRed(sText){
    if (!sText) {
        $("#confirm #message #message_textarea #message_button #abort").addClass('dn');
        return false;
    }
    //$("#confirm #message #message_textarea #message_button #abort").removeClass('dn');
    $("#confirm #message #message_textarea #message_button #abort").val(sText);
}

/**
 * Hide abort button if action can only be confirmed
 */
function hideConfirmationBtnRed(){
    $("#abort").fadeOut(250);
}

function showConfirmationBtnRed(){
    $("#abort").fadeIn(250);
}

/**
 *
 * Changes green button text
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 * @param {String} sText
 */
function changeConfirmationBtnGreen(sText){
    if (!sText) {
        $("#confirm #message #message_textarea #message_button #confirm").addClass('dn');
        return false;
    }
    $("#confirm #message #message_textarea #message_button #confirm").removeClass('dn');
    $("#confirm #message #message_textarea #message_button #confirm").val(sText);
}

/**
 * Switches green Button to Loader and back
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 */
function resetConfirmationBtnGreen(){
    $('.msg_window_loader').css('display', 'none');
    $('#confirmed').fadeIn(250);
}

/**
 * Close confirmation Box
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 */
function closeConfirmation(){
   // window.scrollTo(10000, 10000);
    $("#message_text").html('');
    $("#confirm, ").fadeOut(500, function(){
		$("body").css("overflow-y", "hidden");
        changeConfirmationIcon('img/info.png');
        changeConfirmationHead('');
        changeConfirmationMain('<br/><br/><div align="center"><img src="../../img/loader1.gif"/></div>');
        changeConfirmationBtnRed('Abort');
        // Redefine Ok Button
        resetCOMVars();
    });
}
