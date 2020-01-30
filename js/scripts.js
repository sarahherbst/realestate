/* //////////////////
** Navigation //// */
$(window).on('load', function() {
	var height 			= window.innerHeight,
		x 				= 0,
		y 				= height / 2,
		curveX 			= 10,
		curveY 			= 0,
		targetX 		= 0,
		xitteration 	= 0,
		yitteration 	= 0,
		menuExpanded 	= false;

	(blob 			= $('.blob')),
	(blobPath 		= $('.blob-path')),
	(hamburger 		= $('.hamburger'));

	$(this).on('mousemove', function(e) {
		x = e.pageX - window.scrollX;
        y = e.pageY - window.scrollX;
	});

	$('.hamburger, .menu-inner').on('mouseenter', function() {
		$(this)
			.parent()
			.addClass('expanded');
		menuExpanded = true;
	});

	$('.menu-inner').on('mouseleave', function() {
		menuExpanded = false;
		$(this)
			.parent()
			.removeClass('expanded');
	});

	function easeOutExpo(currentIteration, startValue, changeInValue, totalIterations) {
		return (
			changeInValue * (-Math.pow(2, -10 * currentIteration / totalIterations) + 1) + startValue
		);
	}

	var hoverZone 		= 150;
	var expandAmount 	= 20;

	function svgCurve() {
		if (curveX > x - 1 && curveX < x + 1) {
			xitteration = 0;
		} else {
			if (menuExpanded) {
				targetX = 0;
			} else {
				xitteration = 0;
				if (x > hoverZone) {
					targetX = 0;
				} else {
					targetX = -((60 + expandAmount) / 100 * (x - hoverZone));
				}
			}
			xitteration++;
		}

		if (curveY > y - 1 && curveY < y + 1) {
			yitteration = 0;
		} else {
			yitteration = 0;
			yitteration++;
		}

		curveX = easeOutExpo(xitteration, curveX, targetX - curveX, 100);
		curveY = easeOutExpo(yitteration, curveY, y - curveY, 100);

		var anchorDistance = 200;
		var curviness = anchorDistance - 40;

		var newCurve2 = "M60,"+height+"H0V0h60v"+((y -  window.scrollY)-anchorDistance)+"c0,"+curviness+","+curveX+","+curviness+","+curveX+","+anchorDistance+"S60,"+(curveY)+",60,"+((y -  window.scrollY)+(anchorDistance*2))+"V"+height+"z";


		blobPath.attr('d', newCurve2);
		blob.width(curveX + 60);

		hamburger.css('transform', 'translate(' + curveX + 'px, ' + (y -  window.scrollY) + 'px)');
		$(document).ready(function() {
			window.requestAnimationFrame(svgCurve);
		});
	}
	$(document).ready(function() {
		window.requestAnimationFrame(svgCurve);
	});
});

$(document).ready(function() {
	const trigger = document.querySelector('.mobilemenu-hamburger');
	const menu = document.querySelector('.mobilemenu-inner');

	function toggleClass() {
		trigger.classList.toggle('active');
		menu.classList.toggle('active');
	}

	trigger.addEventListener('click', toggleClass);
	menu.addEventListener('click', toggleClass);
	window.addEventListener('keyup', function(e) {
		if (menu.classList.contains('active') && e.keyCode === 27) {
			toggleClass();
		}
	});
});

window.onscroll = function(){

	var navigationbar = document.getElementById('navigationbar');

	navigationbar.classList.add('bg-white');
	navigationbar.classList.remove('bg-transparent');
};

/* ////////////////////////////////////
** Teaser Carousel TouchSupport//// */
$(document).ready(function() {
	$('#carousel-teaser').swipe( {
		swipeLeft:function(event, direction, distance, duration, fingerCount) {
			$('#carousel-teaser').carousel('next');
		},
		swipeRight:function(event, direction, distance, duration, fingerCount) {
			$('#carousel-teaser').carousel('prev');
		},
		excludedElements: '.h5, a, .btn',
	});
});


/* ////////////////////////////
** Artikel Carousel //// */
$(document).ready(function () {
    var itemsMainDiv = ('.multi-carousel');
    var itemsDiv = ('.multi-carousel-inner');
    var itemWidth = '';

    $('.leftLst, .rightLst').click(function () {
        var condition = $(this).hasClass('leftLst');
        if (condition)
            click(0, this);
        else
            click(1, this)
    });

    ResCarouselSize();

    $(window).resize(function () {
        ResCarouselSize();
    });

    //this function define the size of the items
    function ResCarouselSize() {
        var incno = 0;
        var dataItems = ('data-items');
        var itemClass = ('.item');
        var id = 0;
        var btnParentSb = '';
        var itemsSplit = '';
        var sampwidth = $(itemsMainDiv).width();
        var bodyWidth = $('body').width();
        $(itemsDiv).each(function () {
            id = id + 1;
            var itemNumbers = $(this).find(itemClass).length;
            btnParentSb = $(this).parent().attr(dataItems);
            itemsSplit = btnParentSb.split(',');
            $(this).parent().attr('id', 'multi-carousel' + id);


            if (bodyWidth >= 1200) {
                incno = itemsSplit[3];
                itemWidth = sampwidth / incno;
            }
            else if (bodyWidth >= 992) {
                incno = itemsSplit[2];
                itemWidth = sampwidth / incno;
            }
            else if (bodyWidth >= 768) {
                incno = itemsSplit[1];
                itemWidth = sampwidth / incno;
            }
            else {
                incno = itemsSplit[0];
                itemWidth = sampwidth / incno;
            }
            $(this).css({ 'transform': 'translateX(0px)', 'width': itemWidth * itemNumbers });
            $(this).find(itemClass).each(function () {
                $(this).outerWidth(itemWidth);
            });

            $('.leftLst').addClass('over');
            $('.rightLst').removeClass('over');

        });
    }

    //this function used to move the items
    function ResCarousel(e, el, s) {
        var leftBtn = ('.leftLst');
        var rightBtn = ('.rightLst');
        var translateXval = '';
        var divStyle = $(el + ' ' + itemsDiv).css('transform');
        var values = divStyle.match(/-?[\d\.]+/g);
        var xds = Math.abs(values[4]);
        if (e == 0) {
            translateXval = parseInt(xds) - parseInt(itemWidth * s);
            $(el + ' ' + rightBtn).removeClass('over');

            if (translateXval <= itemWidth / 2) {
                translateXval = 0;
                $(el + ' ' + leftBtn).addClass('over');
            }
        }
        else if (e == 1) {
            var itemsCondition = $(el).find(itemsDiv).width() - $(el).width();
            translateXval = parseInt(xds) + parseInt(itemWidth * s);
            $(el + ' ' + leftBtn).removeClass('over');

            if (translateXval >= itemsCondition - itemWidth / 2) {
                translateXval = itemsCondition;
                $(el + ' ' + rightBtn).addClass('over');
            }
        }
        $(el + ' ' + itemsDiv).css('transform', 'translateX(' + -translateXval + 'px)');
    }

    //It is used to get some elements from btn
    function click(ell, ee) {
        var Parent = '#' + $(ee).parent().attr('id');
        var slide = $(Parent).attr('data-slide');
        ResCarousel(ell, Parent, slide);
    }

});


/* ////////////////////////////////////
** Berater Carousel TouchSupport//// */
$(document).ready(function() {
	$('#carousel-consultant').swipe( {
		swipeLeft:function(event, direction, distance, duration, fingerCount) {
			$('#carousel-consultant').carousel('next');
		},
		swipeRight:function(event, direction, distance, duration, fingerCount) {
			$('#carousel-consultant').carousel('prev');
		},
		excludedElements: 'a, p',
	});
});

$('#carousel-consultant').on('slide.bs.carousel', function (e) {
    var $e = $(e.relatedTarget);
    var idx = $e.index();
    var itemsPerSlide = 2;
    var totalItems = $('.carousel-item').length;

    if (idx >= totalItems-(itemsPerSlide-1)) {
        var it = itemsPerSlide - (totalItems - idx);
        for (var i=0; i<it; i++) {
            // append slides to end
            if (e.direction=="left") {
                $('.carousel-item').eq(i).appendTo('.carousel-inner');
            }
            else {
                $('.carousel-item').eq(0).appendTo('.carousel-inner');
            }
        }
    }
});


/* //////////////////
** Video ///////// */
var youtubePlayer;
function onYouTubeIframeAPIReady() {
	youtubePlayer = new YT.Player('youtube-video');
}

$.getScript('https://www.youtube.com/player_api');

$('.video-watch').on('click', function(e) {
	e.preventDefault();
	$('.video-hero').addClass('playing-video');
	setTimeout(function() {
		youtubePlayer.playVideo();
	}, 900);
});

$('.video-video-close').on('click', function(e) {
	e.preventDefault();
	$('.video-hero').removeClass('playing-video');
	youtubePlayer.pauseVideo();
});


/* //////////////////
** Galerie /////// */
$('.gallery ul li a').click(function() {
	var itemID = $(this).attr('href');
	$('.gallery ul').addClass('item_open');
	$(itemID).addClass('item_open');
	var rowHeight = $('.item_open > .row').outerHeight();
	$('#galerie').css('min-height', rowHeight);
	return false;
});
$('.gallery-close').click(function() {
	$('.port, .gallery ul').removeClass('item_open');
	$('#galerie').css('min-height', 'auto');
	return false;
});

$('.gallery ul li a').click(function() {
	$('html, body').animate({
		scrollTop: parseInt($('#top').offset().top)
	}, 400);
});

if ($('.item_open').length){
	var rowHeight = $('.item_open > .row').outerHeight();
	$('#galerie').css('min-height', rowHeight);
}
$(window).resize(function() {
	if ($('.item_open').length){
		var rowHeight = $('.item_open > .row').outerHeight();
		$('#galerie').css('min-height', rowHeight);
	}
});


/* //////////////////
** Checkliste //// */
$('.list-item-check').on('click', function() {
	$(this).parent().parent().toggleClass('is-checked');
});
$('.list-item-collapse-btn').on('click', function() {
	$(this).children().toggleClass('open');
});
$('.list-item-collapse-title').on('click', function() {
	$(this).next().children().toggleClass('open');
});


$(document).ready(function(){
	// Der Button wird mit JavaScript erzeugt und vor dem Ende des body eingebunden.
	var back_to_top_button = ['<a href="#top" class="back-to-top btn-primary py-2"> &uarr; </a>'].join('""');
	$('body').append(back_to_top_button)

	// Der Button wird ausgeblendet
	$('.back-to-top').hide();

	// Funktion für das Scroll-Verhalten
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) { // Wenn 100 Pixel gescrolled wurde
				$('.back-to-top').fadeIn();
			} else {
				$('.back-to-top').fadeOut();
			}
		});

		$('.back-to-top').click(function () { // Klick auf den Button
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
});

// Smooth Scrolling
$('a[href^="#"]').on('click',function(e) {
	e.preventDefault();
	var target = this.hash;
	var $target = $(target);

	$('html, body').stop().animate({
		'scrollTop': $target.offset().top - 20
	}, 500, 'swing', function () {
		window.location.hash = target;
	});
});
