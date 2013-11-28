$(document).ready( function() {

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
    $('input[type=text], input[type=password]').live('focusin', function() {
        sWidthBefore = $(this).css('width');
        $(this).addClass('form_active');
        $(this).animate({
            width: '310'
        }, 500);
        sTmpStore = $(this).val();
        $(this).val('');
    }).live('focusout', function() {
        $(this).animate({
            width: sWidthBefore
        }, 500);
        $(this).removeClass('form_active');
        if ($(this).val() == '') {
            $(this).val(sTmpStore);
        }
    });
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
    $('input[type=text], input[type=password]').focusin( function() {
        sWidthBefore = $(this).css('width');
        $(this).addClass('form_active');
        $(this).animate({
            width: '310'
        }, 500);
        sTmpStore = $(this).val();
        $(this).val('');
    }).focusout( function() {
        $(this).animate({
            width: sWidthBefore
        }, 500);
        $(this).removeClass('form_active');
        if ($(this).val() == '') {
            $(this).val(sTmpStore);
        }
    });
    $('a.user_register').live('click', function() {
        contentLoader();
        updateArea(this, '#content', {
            'target': 'content'
        });

        return false;
    });
    $('#login_form').submit( function() {
        var sFormData = $(this).serialize();
        var bError = false;
        $.each($('.req'), function() {
            if ($(this).val() == '') {
                $(this).addClass('form_error');
                bError = true;
            } else {
                $(this).removeClass('form_error');
            }
        });
        if (!bError) {
            $.post($(this).attr('action'), sFormData, function(data) {
                $('#content').html(data);

                return false;
            });
        }
        return false;
    });
    $('input[name=password]').replaceWith('<input type="password"/ name="password" class="tia_text req" maxlength="' +
    $('input[name=password]').attr('maxlength') +
    '"/>');

});
