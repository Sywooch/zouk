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


function setCarouselByEnv(env) {
    if (env != oldEnv) {
        if (env == 'lg') {
            if (!$carouselPromotion.data('is-slick')) {
                if (!$carouselPromotion.data('is-slick')) {
                    $carouselPromotion.data('is-slick', true).removeClass('hide').slick({
                        infinite: true,
                        slidesToShow: 5
                        // slidesToScroll: 1,
                    });
                }
            } else {
                $carouselPromotion.slick('slickSetOption', 'slidesToShow', 5, true);
                oldEnv = env;
            }
        } else if (env == 'md') {
            if (!$carouselPromotion.data('is-slick')) {
                if (!$carouselPromotion.data('is-slick')) {
                    $carouselPromotion.data('is-slick', true).removeClass('hide').slick({
                        infinite: true,
                        slidesToShow: 4
                        // slidesToScroll: 1,
                    });
                }
            } else {
                $carouselPromotion.slick('slickSetOption', 'slidesToShow', 4, true);
                oldEnv = env;
            }
        } else if (env == 'sm') {
            if (!$carouselPromotion.data('is-slick')) {
                if (!$carouselPromotion.data('is-slick')) {
                    $carouselPromotion.data('is-slick', true).removeClass('hide').slick({
                        infinite: true,
                        slidesToShow: 3
                        // slidesToScroll: 1,
                    });
                }
            } else {
                $carouselPromotion.slick('slickSetOption', 'slidesToShow', 3, true);
                oldEnv = env;
            }
        } else if (env == 'xs') {
            if (!$carouselPromotion.data('is-slick')) {
                if (!$carouselPromotion.data('is-slick')) {
                    $carouselPromotion.data('is-slick', true).removeClass('hide').slick({
                        infinite: true,
                        slidesToShow: 2
                        // slidesToScroll: 1,
                    });
                }
            } else {
                $carouselPromotion.slick('slickSetOption', 'slidesToShow', 2, true);
                oldEnv = env;
            }
        }
    }
}

$(document).ready(function(){
    $carouselPromotion.find('.block-promo').each(function() {
        var $this = $(this);
        if (typeof $this.data('is-create-img') == "undefined") {
            var src = $this.data('img-url');
            var $obj = $('<img>').attr('src', src).attr('width', '100%');
            if ($this.hasClass('block-promo-prozouk')) {
                var $blockSocial = $this.find('.block-promo-social');
                $blockSocial.before($obj);
                $blockSocial.find('a').each(function() {
                    var $a = $(this);
                    var $obj = $('<img>').attr('src', $a.data('img-url')).attr('width', '100%');
                    $a.append($obj);
                });
            } else {
                $this.find('a').append($obj);
            }
            $this.data('is-create-img', true);
        }
    });

    setCarouselByEnv(findBootstrapEnvironment());

    $carouselPromotion.on('init', function() {
        isInit = true;
    });

    $(window).resize(function() {
        setCarouselByEnv(findBootstrapEnvironment());
    });
});
