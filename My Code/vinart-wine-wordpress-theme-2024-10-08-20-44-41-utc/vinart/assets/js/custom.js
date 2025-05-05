(function ($) {
    "use strict";

	$(document).ready(function() {
		"use strict";
	
		// Scroll to top
		$("a[href='#vinart-top']").on('click', function(e) {
			e.preventDefault();
			$("html, body").animate({ scrollTop: 0 }, "slow");
		});
	
		// Scroll top & sticky header
		var $siteHeader = $('.site-header');
		var $siteFooter = $('.site-footer');
	
		if ($siteHeader.length && $siteFooter.length) {
			var $body = $('body');
	
			$(window).on('scroll', function() {
				var scrollTop = $(window).scrollTop();
				var footerOffsetTop = $siteFooter[0].getBoundingClientRect();
				
				$siteHeader.toggleClass('sticky', scrollTop > 0);
				$body.toggleClass('is-scrolled', scrollTop > 100);
				$body.toggleClass('is-over-footer', footerOffsetTop.y < 0);
			});
		}
	});
	

    // Trigger overlay search form
    $(document).ready(function() {
        var toggleSearchBox = $('.toggle-search-box');
        var searchOverlay = $('.searchform-overlay-wrapper');
        var closeOverlay = $('.close-overlay');
        var searchForm = searchOverlay.find('form');

        // Listen for clicks on the toggle search box trigger
        if (toggleSearchBox.length && searchOverlay.length) {
            toggleSearchBox.on('click', function() {
                searchOverlay.toggleClass('is-visible');
            });
        }

        // Listen for clicks on the close overlay link
        if (closeOverlay.length && searchOverlay.length) {
            closeOverlay.on('click', function() {
                searchOverlay.removeClass('is-visible');
            });
        }

        // Prevent clicks inside the form from closing the overlay
        if (searchOverlay.length) {
            searchOverlay.on('click', function(event) {
                event.stopPropagation();
            });

            searchForm.on('click', function(event) {
                event.stopPropagation();
            });
        }
    });

    $(window).on("elementor/frontend/init", function() {
        // CartDrawer
        var cartDrawerToggle = function($scope, $) {
            $scope.find('.cart-drawer-widget').each(function() {
                var selector = $(this),
                    toggle = selector.find('#cart-drawer-trigger'),
                    overlay = selector.find('#panelOverlay'),
                    close = selector.find('#closeDrawerbtn'),
                    wrapper = selector.find('#cartDrawer');
                toggle.on('click', function(e) {
                    e.preventDefault();
                    overlay.toggleClass('open');
                    wrapper.toggleClass('open');
                });
                overlay.on('click', function(e) {
                    overlay.toggleClass('open');
                    wrapper.removeClass('open');
                });
                close.on('click', function(e) {
                    overlay.toggleClass('open');
                    wrapper.removeClass('open');
                });
            });
        };

        elementorFrontend.hooks.addAction("frontend/element_ready/okthemes-cart-drawer-widget.default", cartDrawerToggle);
    });

    $(document).ready(function() {
        var $body = $('body');
        var $scrollWrap = $('.inertia');

        if ($scrollWrap.length) {
            function setBodyHeight() {
                var totalHeight = $scrollWrap[0].scrollHeight / 2;
                $body.height(totalHeight);
            }

            $(window).on('resize load', setBodyHeight);
            setBodyHeight();

            var speed = 0.04;
            var offset = 0;

            function smoothScroll() {
                offset += ($(window).scrollTop() - offset) * speed;
                gsap.to($scrollWrap, {
                    y: -offset,
                    ease: "power1.out",
                    overwrite: true,
                    duration: 0.1,
                    force3D: true
                });
                requestAnimationFrame(smoothScroll);
            }

            smoothScroll();
        }
    });
})(jQuery);
