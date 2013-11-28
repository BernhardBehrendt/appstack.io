
/**
 * Functionality for formes build from a table
 *
 * - script validates the data
 * - script checks datatype
 * - script handels events like focusin, focusout
 * - script handle account activation
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek 2010
 * @version 1.0.0
 *
 */
if (typeof(load_register) == 'undefined') {
	// protection for single script execution
    var load_register = true;
    
    $(document).ready(function(){
    
        // Empty all text fields
        $('input[type=text], input[type=password]').val('');
        /**
         * Standard functionality for emty input fields and replace value on change nothing
         * Outer variable is needed for temorary store value
         *
         * @author Bernhard Bezdek
         * @copyright Bernhard Bezdek 2010
         * @version 1.0.0
         *
         */
        var sTmpStore;
        var sWidthBefore = '';
        $('input[type=text], input[type=password]').live('focusin', function(){
            sWidthBefore = $(this).css('width');
            $(this).addClass('form_active');
            $(this).animate({
                width: '310'
            }, 500);
            sTmpStore = $(this).val();
            $(this).val('');
        }).live('focusout', function(){
            $(this).animate({
                width: sWidthBefore
            }, 500);
            $(this).removeClass('form_active');
            if ($(this).val() == '') {
                $(this).val(sTmpStore);
            }
        });
        $('#reg_form').submit(function(){
            var oThis = this;
            
            var sForm = $(oThis).serialize();
            var bError = false;
            $.each($('.req'), function(){
                if ($(this).val() == '') {
                    $(this).addClass('form_error');
                    bError = true;
                }
                else {
                    $(this).removeClass('form_error');
                }
            });
            if (!bError) {
                $('#reg_form').fadeOut(500, function(){
                    $.post($(oThis).attr('action'), sForm, function(data){
                    
                        $('#register_result').remove();
                        $('#content').append('<div id="register_result">' + data + '</div>');
                        
                        return false;
                    });
                    return false;
                });
            }
            else {
                return false;
            }
            
            return false;
        });
        $('.form_back').live('click', function(){
            $('#register_result').fadeOut(500, function(){
                backToForm('#reg_form');
            });
            return false;
        });
        $('#form_confirm').live('click', function(){
            var bIsActive = false;
            if (!bIsActive) {
                bIsActive = true;
                $.get($(this).attr('href'), function(data){
                    $('#register_result').html(data);
                    return false;
                });
            }
            return false;
        });
    });
    function backToForm(oForm){
        $(oForm).fadeIn(500, function(){
        });
    }
}
