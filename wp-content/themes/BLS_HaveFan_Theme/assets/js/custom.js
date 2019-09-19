jQuery(document).ready(function() {
	var scrollLink = jQuery('.scroll');

	// Smooth scrolling
	scrollLink.click(function(e) {
		e.preventDefault();
		jQuery('.um-sub-menu li').removeClass('active');
		jQuery(this).parent().addClass('active');
		jQuery('body,html').animate(
			{
				scrollTop: jQuery(this.hash).offset().top
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
				scrollTop: jQuery(this.hash).offset().top
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
	//jQuery(".about-information").prepend('<div class="um-shadow-about-header"><h6>Information</h6></div>');
	// jQuery('.about-information').attr('id', 'information');

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

		jQuery('.about-passion').prepend(
			'<div class="um-shadow-about-header"><h6>Passions</h6></div>'
		);

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
    console.log(sidebar)
		var windowWidth = window.innerWidth;

		if (windowWidth > 768) {
			jQuery(sidebar).stick_in_parent({ 
        offset_top: 64 + 64 + 20,
        // recalc_every: true
      });
		} else {
			jQuery(sidebar).trigger("sticky_kit:detach");
    }
    jQuery(window).resize(function () {
      setSidebarSticky(sidebar)
    })
  }
  setSidebarSticky('.side-container')
});
