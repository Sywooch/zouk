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

var $carouselPromotion = $('.carousel-promotion');

function setCarusel() {
    $carouselPromotion.slick({
        dots: false,
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2
                }
            }
        ]
    });
}

$(document).ready(function(){

    setCarusel();

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


});
