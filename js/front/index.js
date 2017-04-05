$(function () {
    if ('0' == intelli.config.gb_allow_wysiwyg) {
        $('#guestbook_form').dodosTextCounter(intelli.config.gb_max_chars,
            {
                counterDisplayElement: 'span',
                counterDisplayClass: 'textcounter_guestbook_form'
            });
        $('.textcounter_guestbook_form').addClass('textcounter').wrap('<p class="help-block text-right"></p>').before(intelli.lang.chars_left + ' ');
    }
});