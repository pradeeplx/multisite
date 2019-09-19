function blsat_menu_userinfo_ajax() {
  jQuery.ajax({
    type: "POST",
    url: cstm_ajax_object.ajaxurl,
    data: { action: "blsat_wp_stats_ajax_online_total" },
    success: function(data) {
      jQuery("#adminmenuwrap").prepend(data);
      jQuery("#adminmenu").height(window.innerHeight - 100);
      var links = jQuery("#wp-admin-bar-user-actions").html();
      jQuery(".mtrl-menu-profile-links .all-links").html(links);
      jQuery("#wp-admin-bar-my-account").remove();
      setTimeout(function() {
        setUserNewMenu();
        setToggelUser();
      }, 1000);
    }
  });
}
blsat_menu_userinfo_ajax();

function setUserNewMenu() {
  var windowWidth = window.innerWidth;

  if (windowWidth <= 600) {
    jQuery(".menu-userinfo").prependTo("#adminmenu");
  } else {
    jQuery(".menu-userinfo").prependTo("#adminmenuwrap");
  }
  //	document.getElementById("wpwrap").style.opacity = "1";
  //jQuery(".loaderParent").hide();
  jQuery(window).resize(function() {
    setUserNewMenu();
  });
}

function setToggelUser() {
  jQuery("a.mtrl-menu-profile-links .open-links")
    .off()
    .click(function() {
      jQuery(this)
        .next()
        .slideToggle();
    });
}

jQuery(window).ready(function($) {
  //	jQuery("<div class='loaderParent'><div class='loader'></div></div>").insertBefore("#wpwrap");

  $("#post-body").append("<div class='bls-wp-post-container'></div>");
  $("#post-body").append("<div class='bls-wp-sidebar'></div>");
  $("#post-body")
    .children()
    .not(".bls-wp-sidebar")
    .not(".bls-wp-post-container")
    .each(function() {
      var hasChild = $(this).find("#side-sortables").length;
      if (hasChild) {
        $(this).appendTo(".bls-wp-sidebar");
      } else {
        $(this).appendTo(".bls-wp-post-container");
      }
    });
});
jQuery(window).load(function() {
  // if (jQuery("#post-body-content").length > 0) {

  // 	jQuery("#wpbody .notice").each(function () {
  // 		if ($(this).hasClass("update-message")) {

  // 		} else {
  // 			jQuery(this).prependTo("#wpbody #post-body-content").addClass('blsat-div')
  // 		}

  // 	})
  // } else {
  // 	jQuery("#wpbody .notice").each(function () {
  // 		if (jQuery(this).hasClass("update-message")) {

  // 		} else {
  // 			jQuery(this).prependTo("#wpbody #post-body-content").addClass('blsat-div')
  // 		}
  // 	})
  // }
  jQuery(".wrap").css("opacity", 1);
  if (jQuery("#post-body-content").length > 0) {
    var allNotice = [];
    jQuery(".update-nag").each(function() {
      var singleNag = jQuery(this);
      allNotice.push(singleNag);
    });
    jQuery(".updated").each(function() {
      var singleUpdate = jQuery(this);
      allNotice.push(singleUpdate);
    });
    jQuery(".error").each(function() {
      var singleError = jQuery(this);
      allNotice.push(singleError);
    });
    jQuery("#wpbody .notice").each(function() {
      var singleNotice = jQuery(this);
      allNotice.push(singleNotice);
    });

    var newNoticeDataString = allNotice.toString();
    var newNoticeDataString = allNotice.toString();
    jQuery('<div class="bls-update-nag"></div>').insertAfter(
      "#post-body-content"
    );
    allNotice.map(el => jQuery(".bls-update-nag").prepend(el));
    //allNotice.map(el => jQuery("#setting-error-tgmpa").prepend(el) )

    allNotice.map(el => jQuery("#titlediv").prepend(el));
  } else {
    var allNotice = [];
    jQuery(".update-nag").each(function() {
      var singleNag = jQuery(this);
      allNotice.push(singleNag);
    });
    jQuery(".updated").each(function() {
      var singleUpdate = jQuery(this);
      allNotice.push(singleUpdate);
    });
    jQuery(".error").each(function() {
      var singleError = jQuery(this);
      allNotice.push(singleError);
    });
    var newNoticeDataString = allNotice.toString();
    jQuery('<div class="bls-update-nag"></div>').insertAfter(
      "#post-body-content"
    );
    allNotice.map(el => jQuery(".bls-update-nag").prepend(el));
    //allNotice.map(el => jQuery("#setting-error-tgmpa").prepend(el) )
  }

  if (jQuery("#postbox-container-2").length > 0) {
    var allNotice = [];
    jQuery(".update-nag").each(function() {
      var singleNag = jQuery(this);
      allNotice.push(singleNag);
    });
    jQuery(".updated").each(function() {
      var singleUpdate = jQuery(this);
      allNotice.push(singleUpdate);
    });
    jQuery(".error").each(function() {
      var singleError = jQuery(this);
      allNotice.push(singleError);
    });
    jQuery("#wpbody .notice").each(function() {
      var singleNotice = jQuery(this);
      allNotice.push(singleNotice);
    });

    var newNoticeDataString = allNotice.toString();
    allNotice.map(el => jQuery("#titlediv").prepend(el));
    jQuery('<div class="bls-update-nag"></div>').insertAfter(
      "#post-body-content"
    );
    allNotice.map(el => jQuery(".bls-update-nag").prepend(el));
    //allNotice.map(el => jQuery("#setting-error-tgmpa").prepend(el) )
  } else {
    var allNotice = [];
    jQuery(".update-nag").each(function() {
      var singleNag = jQuery(this);
      allNotice.push(singleNag);
    });
    jQuery(".updated").each(function() {
      var singleUpdate = jQuery(this);
      allNotice.push(singleUpdate);
    });
    jQuery(".error").each(function() {
      var singleError = jQuery(this);
      allNotice.push(singleError);
    });
    var newNoticeDataString = allNotice.toString();
    jQuery('<div class="bls-update-nag"></div>').insertAfter(
      "#post-body-content"
    );
    allNotice.map(el => jQuery(".bls-update-nag").prepend(el));
    //allNotice.map(el => jQuery("#setting-error-tgmpa").prepend(el) )
  }
  setInterval(function() {
    jQuery(".notice").css("opacity", 1);
    jQuery(".updated").css("opacity", 1);
    jQuery(".error").css("opacity", 1);
    jQuery(".update-nag").css("opacity", 1);
  }, 300);
  jQuery(".wp-list-table").each(function() {
    var thisTable = jQuery(this);
    thisTable
      .wrap("<div class='res-table-wrapper'></div>")
      .removeClass("wp-list-table")
      .addClass("res-table");
  });
  jQuery(".widefat:not(.res-table)").each(function() {
    var thisTable = jQuery(this);
    thisTable
      .wrap("<div class='res-table-wrapper'></div>")
      .removeClass("wp-list-table")
      .addClass("res-table");
  });
  var oldScroll = 0;

 

  var div_height= jQuery('#adminmenuwrap').height();
 
          

  setTimeout(function() {
    var sidebar = jQuery(".bls-wp-sidebar");
    var mainContainer = jQuery(".bls-wp-post-container");
    function scrollfn() {
      var windowWidth = jQuery( window ).width();
      
              var fullContainer = jQuery("#wpbody-content")
      if(windowWidth > 782 ){
      var topOffset = jQuery("#wpadminbar").outerHeight();
            var sidebarHeight = sidebar.outerHeight();
            var mainContainerHeight = mainContainer.outerHeight();
             var wpbody_height = fullContainer.height();
              var adminmenuwrap_height = jQuery("#adminmenuwrap").height();
               
            if(adminmenuwrap_height > wpbody_height ){

                fullContainer.stick_in_parent({
                  offset_top: 10 + topOffset
                   
                });
            }
            else{
              if (sidebarHeight > mainContainerHeight) {
                setSticky(
                  mainContainer,
                  jQuery("#wpfooter"),
                  topOffset,
                  1024,
                  jQuery("#poststuff")
                );
                mainContainer.stick_in_parent({
                  offset_top: 10 + topOffset
                   
                });
                
              } else if (sidebarHeight < mainContainerHeight) {
                setSticky(
                  sidebar,
                  jQuery("#wpfooter"),
                  topOffset,
                  1024,
                  jQuery("#poststuff")
                );
                sidebar.stick_in_parent({
                  offset_top: 10 + topOffset
                  // recalc_every: 1
                });
                 
              }
            }
      }else{

        sidebar.trigger("sticky_kit:detach");
        mainContainer.trigger("sticky_kit:detach");
        fullContainer.trigger("sticky_kit:detach");
      }
    }

    var preheight = jQuery(".bls-wp-sidebar").height();
    var adminmenuwrap
    jQuery(window).scroll(function() {
      var changeheight = jQuery(".bls-wp-sidebar").height();
      if (preheight !== changeheight) {
        preheight = changeheight;
        sidebar.trigger("sticky_kit:detach");
        mainContainer.trigger("sticky_kit:detach");
        scrollfn();
      }
    }); 
    scrollfn();
  }, 300);
});

//sticky side bar code
function setSticky(
  $sticky,
  $stickyStopper,
  stickOffset = 32,
  minWidth = 0,
  $wrapper
) {
  
  console.log({
    $sticky,
    $stickyStopper,
    stickOffset,
    minWidth,
    $wrapper
  });
  
}