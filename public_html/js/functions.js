(function ($) {

	"use strict";

	$(document).ready(function () {
		$('.header').addClass("sticky"); // Header baştan sticky olacak ve değişmeyecek
	});

	// Sticky sidebar
	$('#sidebar').theiaStickySidebar({
		minWidth: 991,
		additionalMarginTop: 100
	});
	
	// Mobile Mmenu
	var $menu = $("nav#menu").mmenu({
		"extensions": ["pagedim-black", "theme-dark"],
		counters: true,
		keyboardNavigation: {
			enable: true,
			enhance: true
		},
		navbar: {
			title: 'MENU'
		},
		navbars: [{position:'bottom',content: ['<a href="#0">© 2026 Puandeks</a>']}]}, 
		{
		// configuration
		clone: true,
		classNames: {
			fixedElements: {
				fixed: "menu_fixed",
				sticky: "sticky"
			}
		}
	});
	var $icon = $("#hamburger");
	var API = $menu.data("mmenu");
	$icon.on("click", function () {
		if ($icon.hasClass("is-active")) {
			API.close(); // açıkken kapat
		} else {
			API.open(); // kapalıysa aç
		}
	});
	
	API.bind("open:finish", function () {
		setTimeout(function () {
			$icon.addClass("is-active");
		}, 100);
	});
	API.bind("close:finish", function () {
		setTimeout(function () {
			$icon.removeClass("is-active");
		}, 100);
	});
	
	// Rotate icons
	$(".box_cat_home").on('mouseenter', function () {
        $(this).find("img").toggleClass("rotate-x");}
	);
	
	// Modal Sign In
	$('#sign-in').magnificPopup({
		type: 'inline',
		fixedContentPos: true,
		fixedBgPos: true,
		overflowY: 'auto',
		closeBtnInside: true,
		preloader: false,
		midClick: true,
		removalDelay: 300,
		closeMarkup: '<button title="%title%" type="button" class="mfp-close"></button>',
		mainClass: 'my-mfp-zoom-in'
	});
	
	// Show Password
	$('#password, #password_in, #password1, #password2').hidePassword('focus', {
		toggle: {
			className: 'my-toggle'
		}
	});
	
	// Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})

	// Forgot Password
	$("#forgot").on('click', function () {
		$("#forgot_pw").fadeToggle("fast");
	});
	
	//Scroll to top
	$(window).on('scroll', function () {
		'use strict';
		if ($(this).scrollTop() != 0) {
			$('#toTop').fadeIn();
		} else {
			$('#toTop').fadeOut();
		}
	});
	$('#toTop').on('click', function () {
		$('body,html').animate({
			scrollTop: 0
		}, 500);
	});
    
    /* Dropdown user logged */
	$('.dropdown-user').hover(function () {
		$(this).find('.dropdown-menu').stop(true, true).delay(50).fadeIn(300);
	}, function () {
		$(this).find('.dropdown-menu').stop(true, true).delay(50).fadeOut(300);
	});
	
	/* Animation on scroll */
	new WOW().init();
	
	//  Video popups
	$('.video').magnificPopup({type:'iframe'});	/* video modal*/
	
	// Image popups
	$('.magnific-gallery').each(function () {
		$(this).magnificPopup({
			delegate: 'a',
			type: 'image',
            preloader: true,
			gallery: {
				enabled: true
			},
			removalDelay: 500, //delay removal by X to allow out-animation
			callbacks: {
				beforeOpen: function () {
					// just a hack that adds mfp-anim class to markup 
					this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
					this.st.mainClass = this.st.el.attr('data-effect');
				}
			},
			closeOnContentClick: true,
			midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
		});
	});
	
	// Accordion
	function toggleChevron(e) {
		$(e.target)
			.prev('.card-header')
			.find("i.indicator")
			.toggleClass('ti-minus ti-plus');
	}
	$('.accordion_2').on('hidden.bs.collapse shown.bs.collapse', toggleChevron);
		function toggleIcon(e) {
        $(e.target)
            .prev('.panel-heading')
            .find(".indicator")
            .toggleClass('ti-minus ti-plus');
    }
	
	// Jquery select
	$('.custom-search-input-2 select, .custom-select-form select').niceSelect();
	
	// Sticky nav inner
	$(window).on('scroll', function () {
		if ($(this).scrollTop() > 1) {
			$('.header_in.is_sticky').addClass("sticky");
		} else {
			$('.header_in.is_sticky').removeClass("sticky");
		}
	});
	
	// Search Mobile horizontal
	$('a.search_mob').on('click', function () {
		$('.search_mob_wp').slideToggle("fast");
	});
	
	// Collapse filters
	$(window).on('load', function () {
		var width = $(window).width();
		if ($(this).width() < 991) {
			$('.collapse#collapseFilters').removeClass('show');
		} else {
			$('.collapse#collapseFilters').addClass('show');
		};
	});
	
	// Range slider
	$('input[type="range"]').rangeslider({
		polyfill: false,
		onInit: function () {
			this.output = $(".distance span").html(this.$element.val());
		},
		onSlide: function (
			position, value) {
			this.output.html(value);
		}
	});
	
	//Footer collapse
	$(window).on('load resize', function () {
		var width = $(window).width();
		if ($(this).width() < 575) {
			$(".collapse_bt_mobile").attr("data-bs-toggle", "collapse");
			$('footer .collapse.show').removeClass('show');
			$('.collapse_bt_mobile').on('click', function () {
				$(this).find(".circle-plus").toggleClass('opened');
			})
			$('.collapse_bt_mobile').on('click', function () {
				$(this).find(".circle-plus").toggleClass('closed');
			})
		} else {
			$('footer .collapse').addClass('show');
			$("footer .collapse_bt_mobile").attr("data-bs-toggle", "");
		};
	});
	
	// Carousels
	$('#carousel').owlCarousel({
		center: true,
		items: 2,
		loop: true,
		margin: 10,
		responsive: {
			0: {
				items: 1,
				dots:false
			},
			600: {
				items: 2
			},
			1000: {
				items: 4
			}
		}
	});
	
	$('#reccomended').owlCarousel({
		center: true,
		items: 2,
		loop: true,
		margin: 0,
		responsive: {
			0: {
				items: 1
			},
			600: {
				items: 2
			},
			767: {
				items: 2
			},
			1000: {
				items: 3
			},
			1400: {
				items: 3
			}
		}
	});
	
	$('#brands').owlCarousel({
		autoplay:true,
		items: 2,
		loop: true,
		margin: 10,
		dots:false,
		nav:false,
		autoplayTimeout: 3000,
		responsive: {
			0: {
				items: 3
			},
			767: {
				items: 4
			},
			1000: {
				items: 6
			},
			1300: {
				items: 7
			}
		}
	});
	
	// Sticky filters
	$(window).on('load resize', function () {
		var width = $(window).width();
		if (width <= 991) {
			$('.sticky_horizontal').stick_in_parent({
				offset_top: 40
			});
		} else {
			$('.sticky_horizontal').stick_in_parent({
				offset_top: 60			
			});
		}
	});	
	
	// Sticky horizontal results list page or detail page
	$("#results,.sticky_horizontal_2").stick_in_parent({
		offset_top: 0
	});
	            
	// Secondary nav scroll
	var $sticky_nav= $('.secondary_nav');
	$sticky_nav.find('a').on('click', function(e) {
		e.preventDefault();
		var target = this.hash;
		var $target = $(target);
		$('html, body').animate({
			'scrollTop': $target.offset().top - 85
		}, 300, 'swing');
	});
	$sticky_nav.find('ul li a').on('click', function () {
		$sticky_nav.find('ul li a.active').removeClass('active');
		$(this).addClass('active');
	});
	
	// Faq section
	$('#faq_cat').theiaStickySidebar({
		minWidth: 991,
		additionalMarginTop: 100
	});
	$('#faq_box a[href^="#"]').on('click', function () {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') 
			|| location.hostname == this.hostname) {
			var target = $(this.hash);
			target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
			   if (target.length) {
				 $('html,body').animate({
					 scrollTop: target.offset().top -195
				}, 300);
				return false;
			}
		}
	});
	$('ul#cat_nav li a').on('click', function () {
		$('ul#cat_nav li a.active').removeClass('active');
		$(this).addClass('active');
	});
	
	// Button show/hide
	$(".btn_filt").on("click", function () {
		var el = $(this);
		el.text() == el.data("text-swap") ? el.text(el.data("text-original")) : el.text(el.data("text-swap"));
		$('html, body').animate({
			scrollTop: $("body").offset().top
		}, 600);
	});
	
})(window.jQuery); 


document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("ul#top_menu li a").forEach(function (el) {
        el.style.color = "#ffffff";
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const categoryList = document.querySelector(".category-list");
    const leftArrow = document.querySelector(".left-arrow");
    const rightArrow = document.querySelector(".right-arrow");

    leftArrow.addEventListener("click", function () {
        categoryList.scrollBy({ left: -200, behavior: "smooth" });
    });

    rightArrow.addEventListener("click", function () {
        categoryList.scrollBy({ left: 200, behavior: "smooth" });
    });
});

document.addEventListener("DOMContentLoaded", function () {
	console.log("JavaScript Çalşıyor! 🚀");
	
	const images = document.querySelectorAll(".review-images img");
  
	images.forEach((image, index) => {
	  setTimeout(() => {
		console.log(`Animasyon ekleniyor: ${index + 1}. resim`);
		image.classList.add("animate");
	  }, index * 500); // Her bir resmin gecikmeli gelmesini sağlar
	});
  });

// ==============================
// Mobile Menu Alignment Fix
// ==============================
document.addEventListener("DOMContentLoaded", function(){
  function fixMobileMenuLayout(){
    const navList = document.querySelector(".main-nav-list");
    if(!navList) return;

    if(window.innerWidth <= 991){
      navList.style.display = "block";
      navList.style.width = "100%";
      navList.style.textAlign = "left";
      navList.querySelectorAll("li").forEach(li=>{
        li.style.display = "block";
        li.style.width = "100%";
        li.style.margin = "10px 0";
      });
      navList.querySelectorAll("a").forEach(a=>{
        a.style.display = "block";
        a.style.width = "100%";
        a.style.padding = "10px 20px";
        a.style.fontSize = "15px";
        a.style.fontWeight = "700";
        a.style.textDecoration = "none";
        a.style.boxSizing = "border-box";
      });
    } else {
      navList.style.display = "flex";
      navList.style.alignItems = "center";
      navList.style.justifyContent = "flex-end";
      navList.style.gap = "15px";
      navList.querySelectorAll("li").forEach(li=>{
        li.style.display = "inline-block";
        li.style.margin = "0";
      });
    }
  }

  fixMobileMenuLayout();
  window.addEventListener("resize", fixMobileMenuLayout);
});
