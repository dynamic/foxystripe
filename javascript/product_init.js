jQuery('#carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    itemWidth: 75,
    itemMargin: 5,
    asNavFor: '#slider'
});
jQuery('#slider').flexslider({
    animation: "slide",
    animationLoop: true,
    controlNav: true,
    directionNav: true,
    pauseOnAction: true,
    pauseOnHover: true,
    slideshow: false,
    start: function (slider) {
        jQuery('body').removeClass('loading');
    }
});

// init shadowbox
Shadowbox.init();