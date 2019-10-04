jQuery(window).load(function() {
	jQuery(".toggle-sidebar-event").click(function(){
	    jQuery(".sidebar-toggle-wraper").toggle();
	    jQuery(".down-sidebar").toggle();
	    jQuery(".up-sidebar").toggle();
	    
	})
	var scrollLink = jQuery('.scroll');





	// Smooth scrolling
	scrollLink.click(function(e) {
		e.preventDefault();
		 
		jQuery('.um-sub-menu li').removeClass('active');
		jQuery(this).parent().addClass('active');
		var tempTop  = 150;
		if( jQuery( window ).width() > 776) {
			if(jQuery('div.um-profile-nav-experience').hasClass('active')){
				 tempTop = 200;
			}
			
		}
	
		jQuery('body,html').animate(
			{

				scrollTop: jQuery(this.hash).offset().top-tempTop
			},
			1000
		);
		
	});

	var scrollAccount = jQuery('.scrollAccount');

	// Smooth scrolling
	scrollAccount.click(function(e) {
		e.preventDefault();
		jQuery('.scollActive').removeClass('active');
		jQuery(this).parent().addClass('active');
		jQuery('body,html').animate(
			{
				scrollTop: jQuery(this.hash).offset().top-140
			},
			1000
		);
	});

	// Active link switching
	jQuery(window).scroll(function() {
		// var scrollbarLocation = jQuery(this).scrollTop();
		// scrollLink.each(function() {
		//   var sectionOffset = jQuery(this.hash).offset().top;
		//   if ( sectionOffset <= scrollbarLocation ) {
		//     jQuery(this).parent().addClass('active');
		//     jQuery(this).parent().siblings().removeClass('active');
		//   }
		// })
	});
	// code for about information header
	var information_id=jQuery("#information_id").html();
	jQuery(".about-information").prepend('<div class="um-shadow-about-header"><h6>Information</h6><p class="sub-heading" id="informationId">'+ information_id +'</p></div>');
	 jQuery('.about-information').attr('id', 'information');

	function getQueryParams(qs) {
		qs = qs.split('+').join(' ');

		var params = {},
			tokens,
			re = /[?&]?([^=]+)=([^&]*)/g;

		while ((tokens = re.exec(qs))) {
			params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
		}

		return params;
	}
	var query = getQueryParams(document.location.search);
	if (query.subtab) {
		setTimeout(function() {
			var tabName = query.subtab + '-tab';
			jQuery('.' + tabName).click();
		}, 800);
	}
	if (query.profiletab == 'main') {
		jQuery('.about-being-a-fan').prepend('<div class="um-shadow-about-header"><h6>Being a Fan</h6></div>');
		jQuery('.about-being-a-fan').attr('id', 'being-a-fan');

		jQuery('.about-biography').prepend('<div class="um-shadow-about-header"><h6>Biography</h6></div>');
		jQuery('.about-biography').attr('id', 'biography');
		var a = jQuery('#passions').html();
		jQuery(a).insertAfter('.um-form');

		jQuery('.about-passion').prepend('<div class="um-shadow-about-header"><h6>Passions</h6></div>');

		// code for add acf form in accout section
		var parsnalForm = jQuery('#acfFormParsnal').html();
		jQuery(
			"<div id='information' class='main-parsnal-wrappe' ><div class='um-shadow p-20 child-parsnal'>" +
				parsnalForm +
				'</div></div>'
		).insertAfter('.um-account .um-form form');

		var being_a_fan_id = jQuery('#being_a_fan_id').html();
		jQuery('#beingAFanId').html(being_a_fan_id);

		var biographyId = jQuery('#biography_id').html();
		jQuery('#biographyId').html(biographyId);
	} else {
		jQuery('.about-being-a-fan').prepend(
			'<div class="um-shadow-about-header"><h6>Being a Fan</h6><p class="sub-heading" id="beingAFanId">Sub Heading</p></div>'
		);
		jQuery('.about-being-a-fan').attr('id', 'being-a-fan');

		jQuery('.about-biography').prepend(
			'<div class="um-shadow-about-header"><h6>Biography</h6><p class="sub-heading" id="biographyId">Sub Heading</p></div>'
		);
		jQuery('.about-passion').prepend(
			'<div class="um-shadow-about-header"><h6>Passions</h6><p class="sub-heading" id="passionId">Sub Heading</p></div>'
		);
		jQuery('.about-biography').attr('id', 'biography');

		var a = jQuery('#passions').html();
		jQuery(a).insertAfter('.um-form');

		// code for add acf form in accout section
		var parsnalForm = jQuery('#acfFormParsnal').html();
		jQuery(
			"<div id='information' class='main-parsnal-wrappe' ><div class='um-shadow p-20 child-parsnal'>" +
				parsnalForm +
				'</div></div>'
		).insertAfter('.um-account .um-form form');

		var being_a_fan_id = jQuery('#being_a_fan_id').html();
		jQuery('#beingAFanId').html(being_a_fan_id);

		var biographyId = jQuery('#biography_id').html();
		jQuery('#biographyId').html(biographyId);

		var passion_id = jQuery('#passion_id').html();
		jQuery('#passionId').html(passion_id);
	}

	//profiletab
	// var list = '<option value="indore">indore</option><option value="indore2">indore2</option><option value="indore3">indore3</option><option value="indore4">indore4</option>'
	// jQuery("#team-names").html(list);

	function setSidebarSticky(sidebar) {
		var windowWidth = window.innerWidth;

		if (windowWidth > 768) {
			jQuery(sidebar).stick_in_parent({
				offset_top: 64 + 64 + 20
				// recalc_every: true
			});
		} else {
			jQuery(sidebar).trigger('sticky_kit:detach');
		}
		jQuery(window).resize(function() {
			setSidebarSticky(sidebar);
		});
	}
	setSidebarSticky('.side-container');

	// sticky sub header code

	var stickyHeaders = (function() {
		var jQuerywindow = jQuery(window),
			jQuerystickies, tempWidth;
		var load = function(stickies) {
			if (typeof stickies === 'object' && stickies instanceof jQuery && stickies.length > 0) {
				jQuerystickies = stickies.each(function() {
					var jQuerythisSticky = jQuery(this).wrap('<div class="followWrap" />');
					tempWidth = jQuerythisSticky.outerWidth()

					jQuerythisSticky
						.data('originalPosition', jQuerythisSticky.offset().top)
						.data('originalHeight', jQuerythisSticky.outerHeight())
						.parent()
						.height(jQuerythisSticky.outerHeight());
				});

				jQuerywindow.off('scroll.stickies').on('scroll.stickies', function() {
					_whenScrolling(tempWidth);
				});
			}
		};

		var _whenScrolling = function(tempWidth) {
			
			jQuerystickies.each(function(i) {
				var jQuerythisSticky = jQuery(this),
					jQuerystickyPosition = jQuerythisSticky.data('originalPosition');
				if (jQuerystickyPosition <= jQuerywindow.scrollTop()) {
					var jQuerynextSticky = jQuerystickies.eq(i + 1),
						jQuerynextStickyPosition =
							jQuerynextSticky.data('originalPosition') - jQuerythisSticky.data('originalHeight');

					jQuerythisSticky.addClass('fixed').css({
						"width": tempWidth
					});

					if (jQuerynextSticky.length > 0 && jQuerythisSticky.offset().top >= jQuerynextStickyPosition) {
						jQuerythisSticky.addClass('absolute').css('top', jQuerynextStickyPosition);
					}
				} else {
					var jQueryprevSticky = jQuerystickies.eq(i - 1);

					jQuerythisSticky.removeClass('fixed').css({
						"width": ""
					});;

					if (
						jQueryprevSticky.length > 0 &&
						jQuerywindow.scrollTop() <=
							jQuerythisSticky.data('originalPosition') - jQuerythisSticky.data('originalHeight')
					) {
						jQueryprevSticky.removeClass('absolute').removeAttr('style');
					}
				}
			});
		};

		return {
			load: load
		};
	})();

	jQuery(function() {
		stickyHeaders.load(jQuery('.followMeBar'));
	});

	jQuery(function(){
	    var dtToday = new Date();
	    
	    var month = dtToday.getMonth() + 1;
	    var day = dtToday.getDate();
	    var year = dtToday.getFullYear();
	    if(month < 10)
	        month = '0' + month.toString();
	    if(day < 10)
	        day = '0' + day.toString();
	    
	    var minDate= year + '-' + month + '-' + day;
	    // var minDate= day + '-' + month + '-' + year;
	    
	    jQuery('#by_date').attr('min', minDate);
	});

	
});




