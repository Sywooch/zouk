function findBootstrapEnvironment() {
    var envs = ['xs', 'sm', 'md', 'lg'];

    var $el = $('<div>');
    $el.appendTo($('body'));

    for (var i = envs.length - 1; i >= 0; i--) {
        var env = envs[i];

        $el.addClass('hidden-'+env);
        if ($el.is(':hidden')) {
            $el.remove();
            return env;
        }
    }
}

var oldEnv = '';
var isInit = false;
var $carouselPromotion = $('.carousel-promotion');


function unslick() {
    if (isInit) {
        $carouselPromotion.slick('reinit');
        isInit = false;
    }
}

function setCarouselByEnv(env) {
    if (env != oldEnv) {
        if (env == 'lg') {
            if (!isInit) {
                $carouselPromotion.removeClass('hide').slick({
                    infinite: true,
                    slidesToShow: 5
                    // slidesToScroll: 1,
                });
            } else {
                $carouselPromotion.slick('slickSetOption', 'slidesToShow', 5, true);
            }
        } else if (env == 'md') {
            if (!isInit) {
                $carouselPromotion.removeClass('hide').slick({
                    infinite: true,
                    slidesToShow: 4
                    // slidesToScroll: 1,
                });
            } else {
                $carouselPromotion.slick('slickSetOption', 'slidesToShow', 4, true);
            }
        } else if (env == 'sm') {
            if (!isInit) {
                $carouselPromotion.removeClass('hide').slick({
                    infinite: true,
                    slidesToShow: 3
                    // slidesToScroll: 1,
                });
            } else {
                $carouselPromotion.slick('slickSetOption', 'slidesToShow', 3, true);
            }
        } else if (env == 'xs') {
            if (!isInit) {
                $carouselPromotion.removeClass('hide').slick({
                    infinite: true,
                    slidesToShow: 2
                    // slidesToScroll: 1,
                });
            } else {
                $carouselPromotion.slick('slickSetOption', 'slidesToShow', 2, true);
            }
        }
        oldEnv = env;
    }
}

$(document).ready(function(){
    setCarouselByEnv(findBootstrapEnvironment());

    $('.carousel-promotion').on('init', function() {
        isInit = true;
    });

    $(window).resize(function() {
        setCarouselByEnv(findBootstrapEnvironment());
    });
});
