(function ($) {
    $('a[data-drupal-link-system-path="destination"]').addClass('is-active');

    $('.wrap-tab-top li a').on('click', function(e) {
        e.preventDefault();

        $(this).parent().addClass('active').siblings('li').removeClass('active');

        var key = $(this).find('span').html().trim();
        if (key == 'all') {
            $('.wrap-tab-content').addClass('hide')
            $('.wrap-tab-content.content-all').removeClass('hide');
            return;
        }
        if (data) {

            $('.wrap-tab-content').removeClass('hide')
            $('.wrap-tab-content.content-all').addClass('hide');

            $('.wrap-tab-content header .heading').html(data[key + '_title']);
            $('.wrap-tab-content header .desc').html(data[key + '_summary']);

            var template = genLocationHtmlFromData(data[key]);
            $('.info ul').html(template);
            initMap();

        }

    });
})(jQuery);
