"use strict";
/* -------------------------------------
 Google Analytics
 change UA-XXXXX-X to be your site's ID.
 -------------------------------------- */
(function (b, o, i, l, e, r) {
    b.GoogleAnalyticsObject = l;
    b[l] || (b[l] =
            function () {
                (b[l].q = b[l].q || []).push(arguments)
            });
    b[l].l = +new Date;
    e = o.createElement(i);
    r = o.getElementsByTagName(i)[0];
    e.src = '//www.google-analytics.com/analytics.js';
    r.parentNode.insertBefore(e, r)
}(window, document, 'script', 'ga'));
ga('create', 'UA-XXXXX-X', 'auto');
ga('send', 'pageview');
/* -------------------------------------
 CUSTOM FUNCTION WRITE HERE
 -------------------------------------- */
$(document).ready(function (e) {
    /* -------------------------------------
     PRETTY PHOTO GALLERY
     -------------------------------------- */
    $("a[data-rel]").each(function () {
        $(this).attr("rel", $(this).data("rel"));
    });
    $("a[data-rel^='prettyPhoto']").prettyPhoto({
        animation_speed: 'normal',
        theme: 'dark_square',
        slideshow: 3000,
        autoplay_slideshow: false,
        social_tools: false
    });
    /* -------------------------------------
     COUNTER
     -------------------------------------- */
    try {
        $('.tg-counters').appear(function () {
            $('.tg-timer').countTo()
        });
    } catch (err) {
    }
        $(window).load(function () {
        /* -------------------------------------
         //        Preloader
         -------------------------------------- */
        $('#status').fadeOut(); // will first fade out the loading animation
        $('.preloader-bg').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
        $('body').delay(350).css({'overflow': 'visible'});
    });
    /* ---------------------------------------
     PORTFOLIO FILTERABLE
     -------------------------------------- */
    var $container = $('.tg-portfolio-content');
    var $optionSets = $('.option-set');
    var $optionLinks = $optionSets.find('a');
    function doIsotopeFilter() {
        if ($().isotope) {
            var isotopeFilter = '';
            $optionLinks.each(function () {
                var selector = $(this).attr('data-filter');
                var link = window.location.href;
                var firstIndex = link.indexOf('filter=');
                if (firstIndex > 0) {
                    var id = link.substring(firstIndex + 7, link.length);
                    if ('.' + id == selector) {
                        isotopeFilter = '.' + id;
                    }
                }
            });
            $container.isotope({
                itemSelector: '.masonry-grid',
                filter: isotopeFilter
            });
            $optionLinks.each(function () {
                var $this = $(this);
                var selector = $this.attr('data-filter');
                if (selector == isotopeFilter) {
                    if (!$this.hasClass('selected')) {
                        var $optionSet = $this.parents('.option-set');
                        $optionSet.find('.selected').removeClass('selected');
                        $this.addClass('selected');
                    }
                }
            });
            $optionLinks.on('click', function () {
                var $this = $(this);
                var selector = $this.attr('data-filter');
                $container.isotope({itemSelector: '.masonry-grid', filter: selector});
                if (!$this.hasClass('selected')) {
                    var $optionSet = $this.parents('.option-set');
                    $optionSet.find('.selected').removeClass('selected');
                    $this.addClass('selected');
                }
                return false;
            });
        }
    }
    var isotopeTimer = window.setTimeout(function () {
        window.clearTimeout(isotopeTimer);
        doIsotopeFilter();
    }, 1000);
    var selected = $('#tg-filterbale-nav > li > a');
    var $this = $(this);
    selected.on('click', function () {
        if (selected.hasClass('selected')) {
            $(this).parent().addClass('current-menu-item').siblings().removeClass('current-menu-item');
        }
    });
    /* ---------------------------------------
     PRODUCT SLIDER
     -------------------------------------- */
    var owl = $("#tg-product-slider");
    owl.owlCarousel({
        items: 4,
        itemsDesktop: [1199, 3],
        itemsDesktopSmall: [979, 3],
        //autoPlay : true,
        slideSpeed: 300,
        paginationSpeed: 400,
        pagination: true,
        navigation: false,
        navigationText: [
            "<i class='fa fa-angle-left'></i>",
            "<i class='fa fa-angle-right'></i>"
        ]
    });
    /* ---------------------------------------
     TEAM SLIDER
     -------------------------------------- */
    var owl = $("#tg-team-slider");
    owl.owlCarousel({
        items: 4,
        itemsDesktop: [1199, 3],
        itemsDesktopSmall: [979, 3],
        //autoPlay : true,
        slideSpeed: 300,
        paginationSpeed: 400,
        pagination: true,
        navigation: false,
        navigationText: [
            "<i class='fa fa-angle-left'></i>",
            "<i class='fa fa-angle-right'></i>"
        ]
    });
    /* ---------------------------------------
     PERMOTION SLIDER
     -------------------------------------- */
    var owl = $("#tg-shop-sale-slider");
    owl.owlCarousel({
        autoPlay: true,
        paginationSpeed: 400,
        singleItem: true,
        navigation: false,
        navigationText: [
            "<i class='fa fa-angle-left'></i>",
            "<i class='fa fa-angle-right'></i>"
        ]
    });
    /* ---------------------------------------
     FILTER PRODUCT BY PRICE
     -------------------------------------- */
    $(function () {
        $("#slider-range").slider({
            range: true,
            min: 0,
            max: 500,
            values: [75, 300],
            slide: function (event, ui) {
                $("#amount").val("$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ]);
            }
        });
        $("#amount").val("$" + $("#slider-range").slider("values", 0) + " - $" + $("#slider-range").slider("values", 1));
    });
    /* ---------------------------------------
     PRODUCT SLIDER
     -------------------------------------- */
    var sync1 = $("#tg-view-slider");
    var sync2 = $("#tg-thumbnail-slider");
    sync1.owlCarousel({
        singleItem: true,
        slideSpeed: 1000,
        navigation: false,
        pagination: false,
        afterAction: syncPosition,
        responsiveRefreshRate: 200,
    });
    sync2.owlCarousel({
        items: 4,
        itemsDesktop: [1199, 4],
        itemsDesktopSmall: [979, 4],
        itemsTablet: [768, 6],
        itemsMobile: [479, 4],
        navigation: true,
        pagination: false,
        navigationText: [
            "<i class='fa fa-angle-left'></i>",
            "<i class='fa fa-angle-right'></i>"
        ],
        responsiveRefreshRate: 100,
        afterInit: function (el) {
            el.find(".owl-item").eq(0).addClass("active");
        }
    });
    function syncPosition(el) {
        var current = this.currentItem;
        $("#tg-thumbnail-slider")
                .find(".owl-item")
                .removeClass("active")
                .eq(current)
                .addClass("active")
        if ($("#tg-thumbnail-slider").data("owlCarousel") !== undefined) {
            center(current)
        }
    }
    $("#tg-thumbnail-slider").on("click", ".owl-item", function (e) {
        e.preventDefault();
        var number = $(this).data("owlItem");
        sync1.trigger("owl.goTo", number);
    });
    function center(number) {
        var sync2visible = sync2.data("owlCarousel").owl.visibleItems;
        var num = number;
        var found = false;
        for (var i in sync2visible) {
            if (num === sync2visible[i]) {
                var found = true;
            }
        }
        if (found === false) {
            if (num > sync2visible[sync2visible.length - 1]) {
                sync2.trigger("owl.goTo", num - sync2visible.length + 2)
            } else {
                if (num - 1 === -1) {
                    num = 0;
                }
                sync2.trigger("owl.goTo", num);
            }
        } else if (num === sync2visible[sync2visible.length - 1]) {
            sync2.trigger("owl.goTo", sync2visible[1])
        } else if (num === sync2visible[0]) {
            sync2.trigger("owl.goTo", num - 1)
        }
    }
    /* -------------------------------------
     PRODUCT INCREASE
     -------------------------------------- */
    $('em.minus').on('click', function () {
        $('#quantity1').val(parseInt($('#quantity1').val(), 10) - 1);
    });
    $('em.plus').on('click', function () {
        $('#quantity1').val(parseInt($('#quantity1').val(), 10) + 1);
    });
    /* -------------------------------------
     MASNORY GALLERY
     -------------------------------------- */
    $('#tg-shop-sidebar').isotope({
        itemSelector: '.tg-widget',
        masonry: {columnWidth: 2}
    });
    /* -------------------------------------
     THEME ACCORDION
     -------------------------------------- */
    $('#accordion .tg-panel-heading a').on('click', function () {
        $('.tg-panel-heading').removeClass('actives');
        $(this).parents('.tg-panel-heading').addClass('actives');
        $('h4').removeClass('actives');
        $(this).parent().addClass('actives');
    });
    /* ---------------------------------------
     HOME SLIDER
     -------------------------------------- */

	/*var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        parallax: true,
        speed: 1000,
        mousewheelControl: true,
        grabCursor: true,
    });*/
	$("#tg-home-slider").owlCarousel({
        autoPlay: true,
        slideSpeed: 300,
        paginationSpeed: 400,
        singleItem: true,
        pagination: false,
        navigation: true,
        navigationText: [
            "<i class='tg-prev fa fa-angle-left'></i>",
            "<i class='tg-next fa fa-angle-right'></i>"
        ]
    });
	
    /* -------------------------------------
     CUSTOM SCROLLING
     -------------------------------------- */
    $(".tg-suggested-products, .bh-sl-loc-list").niceScroll({
        smoothscroll: true,
        cursorborder: "1px solid #82b440",
        cursorcolor: "#82b440",
        autohidemode: false,
        cursorborderradius: "0"
    });
    /* -------------------------------------
     CALCULATOR
     -------------------------------------- */
    jQuery("#tg-btnreset").on('click', function (event) {
        jQuery(".calculator-table input").val('');
        po_calculator();
        return false;
    });
    $(".calculator-table").on('input', function () {
        po_calculator();
    })
    $("#tg-addrows").on('click', function (event) {
        var a = jQuery("#calculator-area.calculator-table > ul > li:last");
        jQuery(a).clone().insertAfter(a);
        po_calculator();
        return false;
    });
    $("#calculator-area").on('click', '.tg-btnremove', function (event) {
        var a = jQuery(".calculator-table li").length;
        if (a > 1) {
            $(this).parents('li').remove();
            po_calculator();
        }
        return false;
    });
    function po_calculator() {
        var totalPoints = 0;
        jQuery(".calculator-table li").each(function (index, el) {
            var a = $(this).find(".box1 input").val();
            var b = $(this).find(".box3 input").val();
            var c = $(this).find(".box4 input").val();
            var t = a * (b * c);
            if (t > 0) {
                $(this).find(".box5 input").val(t);
                totalPoints += parseInt(t);
            } else {
                $(this).find(".box5 input").val('')
            }
        });
        jQuery("#cal-total").val(totalPoints)
    }
	 /* -------------------------------------
     navigation toggle
     -------------------------------------- */
	// ---------- DropDown On Hover ---------- //
    $('#nav-list-inner li.dropdown').on('hover', function () {
        $(this).find('.dropdown-menu').stop(true, true).delay(100).fadeIn(200);
    }, function () {
        $(this).find('.dropdown-menu').stop(true, true).delay(300).fadeOut(200);
    });
    // ---------- DropDown On Hover ---------- //


    // ---------- Navigation Toggle Function ---------- //
    $(".side-bar-btn").on('click',function () {
        $(".navigation").animate({
            width: "toggle"
        });
    });
    // ---------- Navigation Toggle Function ---------- //
    // ------- Menu ------- //
   
    // ------- Menu ------- //

    // ------- Select Dropdow ------- //
    (function () {
        [].slice.call(document.querySelectorAll('select.tg-select')).forEach(function (el) {
            new SelectFx(el);
        });
    })();
    // ------- Select Dropdow ------- //

    if($('.messages--error').length > 0) {
        var data = $('.messages--error').html();
        $('.data-error-msg').html(data);
        $('.messages--error').remove();
        $('.data-error-msg').addClass('messages--error');
        $('.data-error-msg .visually-hidden').remove();
    }
    
});

/* -------------------------------------
 STORE LOCATOR
 -------------------------------------- */
var path_file_map = (typeof path_map != 'undefined' ? path_map : '');
$('#map-container').storeLocator({
    'dataType': 'json',
    'dataLocation': path_file_map + 'js/locations.json',
    'defaultLoc': true,
    'defaultLat': '10.782518',
    'defaultLng': '106.672872',
    "infowindowTemplatePath": path_file_map + "html/storestructure.html",
    "listTemplatePath": path_file_map + "html/storelist.html",
    "markerImg": path_file_map + "images/po-mapmarker.png",
    "markerDim": {height: 40, width: 32},
    "mapSettings": {zoom: 14, mapTypeId: google.maps.MapTypeId.ROADMAP}
});

