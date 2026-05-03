(function ($) {
	"use strict";

/*=============================================
	=    		 Preloader			      =
=============================================*/
function preloader() {
	$('#ctn-preloader').addClass('loaded');
	$("#loading").fadeOut(500);
	// Una vez haya terminado el preloader aparezca el scroll

	if ($('#ctn-preloader').hasClass('loaded')) {
		// Es para que una vez que se haya ido el preloader se elimine toda la seccion preloader
		$('#preloader').delay(900).queue(function () {
			$(this).remove();
		});
	}
}
$(window).on('load', function () {
	preloader();
	mainSlider();
	aosAnimation();
	popupModal();
	wowAnimation();
});



/*=============================================
	=    		Mobile Menu			      =
=============================================*/
//SubMenu Dropdown Toggle
if ($('.menu-area li.dropdown ul').length) {
	$('.menu-area .navigation li.dropdown').append('<div class="dropdown-btn"><span class="fas fa-angle-down"></span></div>');

}

//Mobile Nav Hide Show
if ($('.mobile-menu').length) {

	var mobileMenuContent = $('.menu-area .main-menu').html();
	$('.mobile-menu .menu-box .menu-outer').append(mobileMenuContent);

	//Dropdown Button
	$('.mobile-menu li.dropdown .dropdown-btn').on('click', function () {
		$(this).toggleClass('open');
		$(this).prev('ul').slideToggle(500);
	});
	//Menu Toggle Btn
	$('.mobile-nav-toggler').on('click', function () {
		$('body').addClass('mobile-menu-visible');
	});

	//Menu Toggle Btn
	$('.mobile-menu .menu-backdrop,.mobile-menu .close-btn').on('click', function () {
		$('body').removeClass('mobile-menu-visible');
	});
}


/*=============================================
	=     Menu sticky & Scroll to top      =
=============================================*/
$(window).on('scroll', function () {
	var scroll = $(window).scrollTop();
	if (scroll < 245) {
		$("#sticky-header").removeClass("sticky-menu");
		$('.scroll-to-target').removeClass('open');

	} else {
		$("#sticky-header").addClass("sticky-menu");
		$('.scroll-to-target').addClass('open');
	}
});



/*=============================================
	=    		 Scroll Up  	         =
=============================================*/
if ($('.scroll-to-target').length) {
  $(".scroll-to-target").on('click', function () {
    var target = $(this).attr('data-target');
    // animate
    $('html, body').animate({
      scrollTop: $(target).offset().top
    }, 1000);

  });
}


/*=============================================
	=    	   Data Background  	         =
=============================================*/
$("[data-background]").each(function () {
	$(this).css("background-image", "url(" + $(this).attr("data-background") + ")")
})



/*=============================================
	=    	   Toggle Active  	         =
=============================================*/
$('.cat-toggle').on('click', function () {
	$('.category-menu').slideToggle(500);
	return false;
});
$('.more_slide_open').slideUp();
$('.more_categories').on('click', function () {
	$(this).toggleClass('show');
	$('.more_slide_open').slideToggle();
});



/*=============================================
	=    		 Main Slider		      =
=============================================*/
function mainSlider() {
	var BasicSlider = $('.slider-active');
	BasicSlider.on('init', function (e, slick) {
		var $firstAnimatingElements = $('.single-slider:first-child').find('[data-animation]');
		doAnimations($firstAnimatingElements);
	});
	BasicSlider.on('beforeChange', function (e, slick, currentSlide, nextSlide) {
		var $animatingElements = $('.single-slider[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
		doAnimations($animatingElements);
	});
	BasicSlider.slick({
		autoplay: true,
		autoplaySpeed: 10000,
		dots: false,
		fade: true,
		arrows: false,
		responsive: [
			{ breakpoint: 767, settings: { dots: false, arrows: false } }
		]
	});

	function doAnimations(elements) {
		var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
		elements.each(function () {
			var $this = $(this);
			var $animationDelay = $this.data('delay');
			var $animationType = 'animated ' + $this.data('animation');
			$this.css({
				'animation-delay': $animationDelay,
				'-webkit-animation-delay': $animationDelay
			});
			$this.addClass($animationType).one(animationEndEvents, function () {
				$this.removeClass($animationType);
			});
		});
	}
}


/*=============================================
	=    		Top Selling Active		     =
=============================================*/
$('.top-selling-active').owlCarousel({
	loop: true,
	margin: 15,
	items: 4,
	autoplay: false,
	autoplayTimeout: 5000,
	autoplaySpeed: 1000,
	navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
	nav: true,
	dots: false,
	responsive: {
		0: {
			items: 1,
			center: false,
			nav: false,
		},
		575: {
			items: 2,
			center: false,
			nav: false,
		},
		768: {
			items: 3,
			center: false,
		},
		992: {
			items: 4,
			center: false,
		},
		1200: {
			items: 4
		},
	}
})


/*=============================================
	=    		Popular Active		      =
=============================================*/
$('.popular-active').slick({
	dots: false,
	infinite: true,
	speed: 1000,
	autoplay: true,
	arrows: true,
	prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-angle-left"></i></button>',
	nextArrow: '<button type="button" class="slick-next"><i class="fas fa-angle-right"></i></button>',
	slidesToShow: 4,
	slidesToScroll: 1,
	responsive: [
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 1,
				infinite: true,
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
				arrows: false,
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
				arrows: false,
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
			}
		},
	]
});


/*=============================================
	=    		Deal Day Active		      =
=============================================*/
$('.deal-day-active').slick({
	dots: false,
	infinite: true,
	speed: 1000,
	autoplay: true,
	arrows: false,
	slidesToShow: 3,
	slidesToScroll: 1,
	responsive: [
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
				infinite: true,
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
				arrows: false,
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
			}
		},
	]
});


/*=============================================
	=    		Brand Active		      =
=============================================*/
$('.brand-active').slick({
	dots: false,
	infinite: true,
	speed: 1000,
	autoplay: true,
	arrows: false,
	slidesToShow: 6,
	slidesToScroll: 2,
	responsive: [
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 5,
				slidesToScroll: 1,
				infinite: true,
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 4,
				slidesToScroll: 1
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 1,
				arrows: false,
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
				arrows: false,
			}
		},
	]
});


/*=============================================
	=    	   Testimonial Active		    =
=============================================*/
$('.testimonial-active').slick({
	dots: true,
	infinite: true,
	speed: 1000,
	autoplay: false,
	centerMode: true,
	centerPadding: '0px',
	arrows: false,
	slidesToShow: 3,
	slidesToScroll: 1,
	responsive: [
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 1,
				infinite: true,
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
			}
		},
	]
});


/*=============================================
	=         Sidebar Product Active        =
=============================================*/
$('.sidebar-product-active').slick({
	dots: false,
	infinite: true,
	speed: 1000,
	autoplay: false,
	arrows: true,
	slidesToShow: 1,
	slidesToScroll: 1,
	prevArrow: '<span class="slick-prev"><i class="fas fa-angle-left"></i></span>',
	nextArrow: '<span class="slick-next"><i class="fas fa-angle-right"></i></span>',
	appendArrows: ".slider-nav",
	responsive: [
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				infinite: true,
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
			}
		},
	]
});


/*=============================================
	=         Related Product Active        =
=============================================*/
$('.related-product-active').slick({
	dots: false,
	infinite: true,
	speed: 1000,
	autoplay: false,
	arrows: true,
	slidesToShow: 4,
	slidesToScroll: 1,
	prevArrow: '<span class="slick-prev"><i class="fas fa-angle-left"></i></span>',
	nextArrow: '<span class="slick-next"><i class="fas fa-angle-right"></i></span>',
	appendArrows: ".slider-nav",
	responsive: [
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 1,
				infinite: true,
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
			}
		},
	]
});


/*=============================================
	=         Product Rating Active        =
=============================================*/
var options = {
	max_value: 5,
	step_size: 1,
	initial_value: 0,
	selected_symbol_type: 'fontawesome_star', // Must be a key from symbols
	cursor: 'default',
	readonly: false,
	change_once: false, // Determines if the rating can only be set once
	ajax_method: 'POST',
	additional_data: {} // Additional data to send to the server
}
$(".rising-rating").rate(options);


/*=============================================
	=    		Odometer Active  	       =
=============================================*/
$('.odometer').appear(function (e) {
	var odo = $(".odometer");
	odo.each(function () {
		var countNumber = $(this).attr("data-count");
		$(this).html(countNumber);
	});
});


/*=============================================
	=    		Magnific Popup		      =
=============================================*/
$('.popup-image').magnificPopup({
	type: 'image',
	gallery: {
		enabled: true
	}
});

/* magnificPopup video view */
$('.popup-video').magnificPopup({
	type: 'iframe'
});


/*=============================================
	=    		Isotope	Active  	      =
=============================================*/
$('.exclusive-active').imagesLoaded(function () {
	// init Isotope
	var $grid = $('.exclusive-active').isotope({
		itemSelector: '.grid-item',
		percentPosition: true,
		masonry: {
			columnWidth: '.grid-sizer',
		}
	});
	// filter items on button click
	$('.product-menu').on('click', 'button', function () {
		var filterValue = $(this).attr('data-filter');
		$grid.isotope({ filter: filterValue });
	});

});
//for menu active class
$('.product-menu button').on('click', function (event) {
	$(this).siblings('.active').removeClass('active');
	$(this).addClass('active');
	event.preventDefault();
});


/*=============================================
	=    	  Countdown Active  	         =
=============================================*/
$('[data-countdown]').each(function () {
	var $this = $(this), finalDate = $(this).data('countdown');
	$this.countdown(finalDate, function (event) {
		$this.html(event.strftime('<div class="time-count day"><span>%D</span>Day</div><div class="time-count hour"><span>%H</span>Hr</div><div class="time-count min"><span>%M</span>Min</div><div class="time-count sec"><span>%S</span>Sec</div>'));
	});
});


/*=============================================
	=    	Shop Details Active  	       =
=============================================*/
$('.shop-details-active').slick({
	slidesToShow: 1,
	slidesToScroll: 1,
	arrows: false,
	dots: false,
	fade: true,
	asNavFor: '.shop-details-nav'
});
$('.shop-details-nav').slick({
	slidesToShow: 4,
	slidesToScroll: 1,
	asNavFor: '.shop-details-active',
	arrows: false,
	dots: false,
	centerMode: true,
	centerPadding: '0px',
	vertical: true,
	focusOnSelect: true,
	responsive: [
		{
			breakpoint: 1200,
			settings: {
				slidesToShow: 4,
				slidesToScroll: 1,
				infinite: true,
				vertical: false,
			}
		},
		{
			breakpoint: 992,
			settings: {
				slidesToShow: 4,
				slidesToScroll: 1
			}
		},
		{
			breakpoint: 767,
			settings: {
				slidesToShow: 4,
				slidesToScroll: 1,
				arrows: false,
			}
		},
		{
			breakpoint: 575,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1,
				arrows: false,
			}
		},
	]
});





/*=============================================
	=    	 Slider Range Active  	         =
=============================================*/
const min_price = $('input[name="min_price"]').val() > 0 ? $('input[name="min_price"]').val() : 40;
const max_price = $('input[name="max_price"]').val() > 0 ? $('input[name="max_price"]').val() : 60000;

$("#slider-range").slider({
	range: true,
	min: 40,
	max: 60000,
	values: [min_price, max_price],
	slide: function (event, ui) {
		$("#amount").val(ui.values[0] + "р. - " + ui.values[1]+"р.");
        $('input[name="min_price"]').prop('value', ui.values[0]);
        $('input[name="max_price"]').prop('value', ui.values[1]);
	}
});
$("#amount").val($("#slider-range").slider("values", 0) + "р. - " + $("#slider-range").slider("values", 1)+"р.");


/*=============================================
	=    		 Aos Active  	         =
=============================================*/
function aosAnimation() {
    if (window.AOS) {
        AOS.init({
            duration: 1000,
            mirror: true,
            once: true,
            disable: 'mobile',
        });
    } else {
        console.error('AOS library is not loaded');
    }
}

/*=============================================
	=      Newsletter Modal Active  	     =
=============================================*/
function popupModal() {
	setTimeout(function () {
		$('#exampleModal').modal('show');
	}, 5000);
}

/*=============================================
	=    		 Wow Active  	         =
=============================================*/
function wowAnimation() {
    if (window.AOS) {
        var wow = new WOW({
            boxClass: 'wow',
            animateClass: 'animated',
            offset: 0,
            mobile: false,
            live: true
        });
        wow.init();
    } else {
        console.error('WOW library is not loaded');
    }
}

$(".product-plus-minus").append('<div class="dec qtybutton">-</div><div class="inc qtybutton">+</div>');
$(".qtybutton").on("click", function () {
	var $button = $(this);
	var oldValue = $button.parent().find("input").val();

	if ($button.text() == "+") {
		var newVal = parseFloat(oldValue) + 1;
	} else {
		// Don't allow decrementing below zero
		if (oldValue > 0) {
			var newVal = parseFloat(oldValue) - 1;

		} else {
			newVal = 0;
		}
	}
	$button.parent().find("input").val(newVal);
});

$(".insurance").click(function(){
	switch ($(this).attr('data-id')) {
		case 'osago':
			$("#widget-osago").removeClass('hidden');
			$("#widget-kasko").addClass('hidden');
			$("#widget-property").addClass('hidden');
		break;
		case 'kasko':
			$("#widget-osago").addClass('hidden');
			$("#widget-kasko").removeClass('hidden');
			$("#widget-property").addClass('hidden');
		break;
		case 'property':
			$("#widget-osago").addClass('hidden');
			$("#widget-kasko").addClass('hidden');
			$("#widget-property").removeClass('hidden');
		break;
	}
	});

document.querySelectorAll('a[href="#"]').forEach( link => link.onclick = event => event.preventDefault() );
})(jQuery);



//////

// (function ($) {
//     "use strict";
//
//     if($('.playlist').length == 0) return;
//
//     var playlist = $( '.playlist' ).mepPlaylist({
//         audioHeight: '40',
//         audioWidth: '100%',
//         videoHeight: '40',
//         videoWidth: '100%',
//         audioVolume: 'vertical',
//         mepPlaylistLoop: true,
//         alwaysShowControls: true,
//         mepSkin: 'mejs-audio',
//         mepResponsiveProgress: true,
//         mepSelectors: {
//             playlist: '.playlist',
//             track: '.track',
//             tracklist: '.tracks'
//         },
//         features: [
//             'meplike',
//             'mepartwork',
//             'mepcurrentdetails',
//             'mepplaylist',
//             'mephistory',
//             'mepprevioustrack',
//             'playpause',
//             'mepnexttrack',
//             'progress',
//             'current',
//             'duration',
//             'volume',
//             'mepicons',
//             'meprepeat',
//             'mepshuffle',
//             'mepsource',
//             'mepbuffering',
//             'meptracklist',
//             'mepplaylisttoggle',
//             'youtube'
//         ],
//         mepPlaylistTracks: [
//             {
//                 "id": "",
//                 "title": "Книга не выбрана",
//                 "except": "",
//                 "link": "#",
//                 "thumb": { "src": "/assets/img/icons/android-chrome-512x512.png" },
//                 "src": "#",
//                 "meta": {
//                     "author": "",
//                     "authorlink": "#",
//                     "date": "01.01.2026",
//                     "category": "DJ",
//                     "play": 300,
//                     "like": 10,
//                     "duration": ""
//                 }
//             }
//         ]
//     });
//
//     // get player, then you can use the player.mepAdd(), player.mepRemove(), player.mepSelect()
//     var player = playlist.find('audio, video')[0].player;
//
//     // event on like btn
//     player.$node.on('like.mep', function(e, trackid){
//         $('[track-id='+trackid+']').toggleClass('is-like');
//     });
//
//     // event on play
//     player.$node.on('play', function(e){
//         updateDisplay();
//     });
//
//     // event on pause
//     player.$node.on('pause', function(e){
//         updateDisplay();
//     });
//
//     // update when pjax end
//     $(document).on('pjaxEnd', function() {
//         updateDisplay();
//     });
//     $(document).ready(function(){
//         var player = sessionStorage.getItem('player');
//         if(player){
//             $(".app-footer").addClass("active");
//         }
//     });
//     // simulate the play btn
//     $(document).on('click.btn', '.btn-playpause', function(e){
//         sessionStorage.setItem('player', true);
//         $(".app-footer").addClass("active");
//         e.stopPropagation();
//         var self = $(this);
//         if( self.hasClass('is-playing') ){
//             self.removeClass('is-playing');
//             player.pause();
//         } else {
//             var item = getItem(self);
//             item && player.mepAdd(item, true);
//         }
//     });
//
//     function updateDisplay(){
//         $('[data-id]').removeClass('active').find('.btn-playpause').removeClass('is-playing').parent().removeClass('active');
//         var track = player.mepGetCurrentTrack();
//         if(!track || !track.id) return;
//         var item = $('[data-id="'+track.id+'"]');
//         if( player.media.paused ){
//             item.removeClass('active').find('.btn-playpause').removeClass('is-playing').parent().removeClass('active');
//         }else{
//             item.addClass('active').find('.btn-playpause').addClass('is-playing').parent().addClass('active');
//         }
//     }
//
//     // get item data, you can use ajax to get data from server
//     function getItem(self){
//         var item = self.closest('.item');
//         // track detail
//         if(!item.attr('data-src')){
//             self.toggleClass('is-playing');
//             $('#tracks').find('.btn-playpause').first().trigger('click');
//             return false;
//         }
//
//         var obj = {
//             meta: {
//                 author: item.find('.item-author').find('a').text()
//                 ,authorlink : item.find('.item-author').find('a').attr('href')
//             }
//             ,src: self.closest('[data-src]').attr("data-src")
//             ,thumb: {
//                 src: item.find('.item-media-content').css("background-image").replace(/^url\(["']?/, '').replace(/["']?\)$/, '')
//             }
//             ,title: item.find('.item-title').find('a').text()
//             ,link: item.find('.item-title').find('a').attr('href')
//             ,id: self.attr("data-id") ? self.attr("data-id") : self.closest('[data-id]').attr("data-id")
//         };
//         return obj;
//     }
//
// })(jQuery);

/* Функция пересчёта размера шрифта */
function fGummaFontSize() {
    /* Увеличиваем размер шрифта, до появления прокрутки */
    while (this.scrollHeight <= this.clientHeight || this.scrollWidth <= this.clientWidth) {
        this.style.fontSize = parseFloat(getComputedStyle(this).fontSize) + 2 + "px";
    }
    /* Уменьшаем размер шрифта, пока прокрутка не исчезнет */
    while ( this.scrollHeight > this.clientHeight || this.scrollWidth > this.clientWidth ) {
        this.style.fontSize = parseFloat(getComputedStyle(this).fontSize) - 1 + "px";
    }
}

/* Функция обхода всех элементов с нужным классом */
function fGummaResizeAll() {
    document.querySelectorAll(".gumma").forEach(el => fGummaFontSize.call(el));
}

document.querySelectorAll(".gumma").forEach(el => el.addEventListener("input", fGummaFontSize));
window.onload = fGummaResizeAll; // Запуск после загрузки контента
window.onresize = fGummaResizeAll; // Запуск при изменении размеров окна и контейнеров

$(document).ready(function() {
    const $input = $('#search-input');
    const $results = $('#autocomplete-results');

    $input.on('input', function() {
        const query = $(this).val().trim();

        if (query.length < 2) {
            $results.hide();
            return;
        }

        // AJAX-запрос к серверу
        $.ajax({
            url: '/ajax_search/', // ваш API-endpoint
            method: 'GET',
            data: {
                q: query,
                group: $('select[name="group"]').val()
            },
            success: function(data) {
                if (data.length > 0) {
                    renderResults(data);
                    $results.show();
                } else {
                    $results.hide();
                }
            },
            error: function() {
                $results.hide();
            }
        });
    });

    // Скрываем подсказки при клике вне поля
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-input, #autocomplete-results').length) {
            $results.hide();
        }
    });

    function renderResults(items) {
        let html = '';

        items.forEach(function(item) {
            html += `
                <div class="autocomplete-item" data-id="${item.id}" data-url="${item.url}">
                    <img src="${item.image}" alt="${item.name}" class="autocomplete-image">
                    <div class="autocomplete-text">${item.name}</div>
                    <div class="autocomplete-price">${item.price} ₽</div>
                </div>
            `;
        });

        $results.html(html);
    }

    // Обработка клика по подсказке
    $results.on('click', '.autocomplete-item', function() {
        const itemId = $(this).data('id');
        const itemUrl = $(this).data('url');

        // Устанавливаем значение в поле ввода
        $input.val($(this).find('.autocomplete-text').text());

        // Переходим на страницу товара или выполняем поиск
        window.location.href = itemUrl;

        $results.hide();
    });
});

$(document).ready(function() {
    const $mobileinput = $('#search-mobile-input');
    const $mobileresults = $('#autocomplete-results-mobile');

    $mobileinput.on('input', function() {
        const query = $(this).val().trim();

        if (query.length < 2) {
            $mobileresults.hide();
            return;
        }

        // AJAX-запрос к серверу
        $.ajax({
            url: '/ajax_search/', // ваш API-endpoint
            method: 'GET',
            data: {
                q: query,
                group: $('select[name="group"]').val()
            },
            success: function(data) {
                if (data.length > 0) {
                    renderResults(data);
                    $mobileresults.show();
                } else {
                    $mobileresults.hide();
                }
            },
            error: function() {
                $mobileresults.hide();
            }
        });
    });

    // Скрываем подсказки при клике вне поля
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-mobile-input, #autocomplete-results-mobile').length) {
            $mobileresults.hide();
        }
    });

    function renderResults(items) {
        let html = '';

        items.forEach(function(item) {
            html += `
                <div class="autocomplete-item" data-id="${item.id}" data-url="${item.url}">
                    <img src="${item.image}" alt="${item.name}" class="autocomplete-image">
                    <div class="autocomplete-text">${item.name}</div>
                    <div class="autocomplete-price">${item.price} ₽</div>
                </div>
            `;
        });

        $mobileresults.html(html);
    }

    // Обработка клика по подсказке
    $mobileresults.on('click', '.autocomplete-item', function() {
        const itemId = $(this).data('id');
        const itemUrl = $(this).data('url');

        // Устанавливаем значение в поле ввода
        $mobileinput.val($(this).find('.autocomplete-text').text());

        // Переходим на страницу товара или выполняем поиск
        window.location.href = itemUrl;

        $mobileresults.hide();
    });
});


