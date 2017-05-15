(function ($) {
    if (__actived_menu_link != '') {
        $('a[href="' + __actived_menu_link + '"]').addClass('is-active');
    }
})(jQuery);