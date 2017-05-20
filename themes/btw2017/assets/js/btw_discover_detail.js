(function ($) {
    $('.discover-tab').click(function(e){
        var _this = $(this);
        var tab = _this.data('tab');
        $('.discover-tab').removeClass('active');
        _this.addClass('active');
        $('.discover-tab-content').addClass('hidden');
        $('#' + tab).removeClass('hidden');
    });
})(jQuery);