// CONSOLE_________________________________________________________________________________________________________
$('.console ').dblclick( function() {
    var iPosition = parseInt($(this).css('left').toLowerCase().replace(/px/, ''));
    if (iPosition == -1240) {
        $(this).animate({
            'left': '5px'
        }, 500);
    } else {
        $(this).animate({
            'left': '-1240px'
        }, 500);
    }
});
$('#themes, #languages').live('change', function() {

    $('.c_output').html('<iframe class="highlight" src="console?brush=' + $('#languages').val() + '&theme=' + $('#themes').val() + '&code=' + $('.c_input').val().replace(/\n/g, '[LBR]').replace(/	/g, '[TAB]') + '"></iframe>');

});
$('.c_input').keyup( function(evt) {
    $('.c_output').html('<iframe class="highlight" src="console?brush=' + $('#languages').val() + '&theme=' + $('#themes').val() + '&code=' + $('.c_input').val().replace(/\n/g, '[LBR]').replace(/	/g, '[TAB]') + '"></iframe>');
});
$('.c_plugins').html(sLists);

var sLists = createSelectList(new Array('DEFAULT', 'DJANGO', 'ECLIPSE', 'EMACS', 'FADETOGREY', 'MDULTRA', 'MIDNIGHT', 'RDARK'), 'themes', '');
sLists += createSelectList(new Array('JAVASCRIPT', 'CSS', 'PHP', 'HTML', 'XML'), 'languages', '');

