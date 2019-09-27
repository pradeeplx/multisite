var GLOBAL_CITY_OBJECT = undefined;
var GLOBAL_COUNTRY_CITY_OBJECT = undefined;
if(jQuery('ul#menu-user-menu li').hasClass('user-experiance-menu')){
    var a=jQuery(".user-experiance-menu a").attr('href');
    jQuery(".user-experiance-menu a").attr('href',a+'?profiletab=experience&subtab=information&um_info_action=edit');

}
jQuery(document).ready(function($) {

 
   function add_loader(){
      var lodarImage = cstmf_object.site_url+'/assets/image/live_loader.gif';
      jQuery('body').append('<div id="hf_inifiniteLoader" class="lodr-modal"><center></center><span><img src="'+lodarImage+'" style="width: 70px;" /></span></div>');
      jQuery("body #hf_inifiniteLoader").show('fast');
   }
   function remove_loader(){
      jQuery("body #hf_inifiniteLoader").hide('1000');
      jQuery("body #hf_inifiniteLoader").remove();
   }


// code for open gallery modal
jQuery(".close-gallery-modal").live('click',function(){
 
	jQuery("body #hf_inifiniteLoader").hide('1000');
    jQuery("body #hf_inifiniteLoader").remove();
})
jQuery(".open-gallery-modal").click(function(){
  var a = jQuery(this).attr('image-url');
 
  var lodarImage = a;
  jQuery('body').append('<div id="hf_inifiniteLoader"><center></center><div class="modal-image-wrapper"><span class="image-modal"><p class="close-gallery-modal">x</p><img src="'+lodarImage+'"  /></span></div></div>');
  jQuery("body #hf_inifiniteLoader").show('fast');
})



   jQuery('#EditHaveFunModal .close').on('click', function(){
    var edit_modal = document.getElementById("EditHaveFunModal");
    edit_modal.style.display = "none";
  });
  jQuery('#HaveFunModal .close').on('click', function(){
    var modal = document.getElementById("HaveFunModal");
    modal.style.display = "none";
  });
   
    function getEventForm(event_id, host_id){
         
        var dataString = 'action=get_event_form&event_id='+event_id+'&host_id='+host_id+'&wp_nonce='+cstmf_object.cstmf_ajax_nonce;
        jQuery.ajax({
            type : "post",
            dataType : 'html',
            url : cstmf_object.ajaxurl,
            data : dataString,
            success: function(res) {
                 
                jQuery('#EditHaveFunModal .havefun-content-wrap').html(res);
                remove_loader(); 
                var edit_modal = document.getElementById("EditHaveFunModal");
                  edit_modal.style.display = "block";
              },
              error: function(error){
                remove_loader(); 
            }
                
        });
    }
   // getEventForm( 456, 1 );
   /** This Pagination for host **/
   jQuery("#host-event-pagination li").on("click", function($){
        
        if(jQuery(this).hasClass('active')){
            return false;
        }
        add_loader();
        var id = jQuery(this).attr('data-id');
        jQuery('.event-pagination li').removeClass('active');
        jQuery(this).addClass('active');
        var host_status = jQuery('#event_host_status').val();
        var editable = jQuery('#event_host_status').attr('data-id');
        var dataString = 'action=get_upcoming_event&paged='+id+'&host_status='+host_status+'&wp_nonce='+cstmf_object.cstmf_ajax_nonce+'&editable='+editable;
        jQuery.ajax({
            type : "post",
            dataType : "json",
            url : cstmf_object.ajaxurl,
            data : dataString,
            success: function(res) {
                if(res.status == 'success') {
                    jQuery('#upcoming-event-section').html(res.response);
                     
                }
                  remove_loader(); 
              },
              error: function(error){
                remove_loader(); 
            }
                
        });
   });

   /** This Pagination for Guest **/
   jQuery("#guest-event-pagination li").on("click", function($){
        
        if(jQuery(this).hasClass('active')){
            return false;
        }
        add_loader();
        var id = jQuery(this).attr('data-id');
        jQuery('.event-pagination li').removeClass('active');
        jQuery(this).addClass('active');
        var host_status = jQuery('#event_host_status').val();
        var editable = jQuery('#event_host_status').attr('data-id');
        var dataString = 'action=get_guest_upcoming_matches&paged='+id+'&host_status='+host_status+'&wp_nonce='+cstmf_object.cstmf_ajax_nonce+'&editable='+editable;
        jQuery.ajax({
            type : "post",
            dataType : "json",
            url : cstmf_object.ajaxurl,
            data : dataString,
            success: function(res) {
                if(res.status == 'success') {
                    jQuery('#upcoming-event-section').html(res.response);
                     
                }
                  remove_loader(); 
              },
              error: function(error){
                remove_loader(); 
            }
                
        });
   });

   jQuery('#submitFORM').submit(ajaxSubmit);

   jQuery('#assing_product').submit(ajaxSubmit);
   function ajaxSubmit() {
	 
  
  var hv_host_event_id = jQuery.trim(jQuery("#hv_host_event_id").val());
  jQuery("#event_action_btn_"+hv_host_event_id).css('display', 'none');
	var hv_title=jQuery("#hv_title").val();
	var hv_price=jQuery("#hv_price").val();
	if(hv_title.length>1){
		if(hv_price>0){
			jQuery("#errorMsg").html('');
			add_loader(); 
			var BookingForm = jQuery(this).serialize();
				jQuery.ajax({
				  action:  'create_host_product',          
				  type:    "POST",
					dataType : "json",
				  url:    cstmf_object.ajaxurl,
				  data:    BookingForm,
				  success: function(data) {
				 
					 remove_loader(); 
					if(data.status == 'success') {
						jQuery('#HaveFunModal .close').trigger('click');
						//alert(data.message);
            launch_toast('Success',data.message);
						 //window.location.replace(data.redirect_url);
					}else{
						//alert(data.message);
            launch_toast('Error',data.message);
					}
					
				  },
				  error: function( error ){
					  remove_loader(); 
				  },
				});
				return false;
		}else{
			jQuery("#errorMsg").html('<p class="um-field-error"> Price should be grater than 0 </p>');
			
		}
	}else{
		jQuery("#errorMsg").html('<p class="um-field-error"> Evant Title is required </p>');
		
	}
	return false;
    
  }
  /** Submit event update form */
  jQuery('#update_product').live('submit',ajaxSubmit1);
   function ajaxSubmit1() {
     
   
    var ProductForm = jQuery(this).serialize();
	var hv_title=jQuery("#u_hv_title").val();
	var hv_price=jQuery("#u_hv_price").val();
	if(hv_title.length>1){
		if(hv_price>0){
			add_loader();
			jQuery.ajax({
			  action:  'update_host_product',
			  type:    "POST",
			  dataType : "json",
			  url:    cstmf_object.ajaxurl,
			  data:    ProductForm,
			  success: function(data) {
				 remove_loader(); 
				if(data.status == 'success') {
				//	alert(data.message);
        launch_toast('Success',data.message);
					//window.location.replace(data.redirect_url);
					return;
				}
				
			  },
			  error: function( error ){
				 remove_loader(); 
         launch_toast('Error','Something was wrong.');
			  },
			});
		}
		else{
			jQuery("#errorMsgs").html('<p class="um-field-error"> Price should be grater than 0 </p>');
		}
	}else{
		
		jQuery("#errorMsgs").html('<p class="um-field-error"> Evant Title is required </p>');
	}
    
     return false;
  }

   
 

    jQuery('.event_action_btn').live('click', function(e){
        e.preventDefault();
        jQuery('#EditHaveFunModal .close').trigger('click');

         var edit_modal = document.getElementById("EditHaveFunModal");
          edit_modal.style.display = "none";
        var event_id = jQuery(this).attr('data-val');
        
        jQuery('#hv_host_event_id').val(event_id);
        var event_title = jQuery('.event-list-'+event_id+ ' .event-title').attr('data-title');
        
        jQuery('#hv_title').val(event_title);
        var modal = document.getElementById("HaveFunModal");
        modal.style.display = "block";
         
    });
   jQuery("span.edit_event_action_btn").live("click", function($){
      
        jQuery('#HaveFunModal .close').trigger('click');
        event_id = jQuery(this).attr('data-val');
        host_id = jQuery(this).attr('data-id');
        getEventForm( event_id, host_id );
    })

   // submit code for where to go form 
   jQuery("#form_id_133").submit(function(e){
       e.preventDefault;
       var data=[];
       var where_to_go_list=jQuery("#acf-field_5d70d18bcf85b").val();
       var user_id=jQuery("#_acf_post_id").val();
       var data={
          where_to_go_list:where_to_go_list,
          user_id:user_id,
          action:  'havefan_save_acf_usermeta',
          wp_nonce:cstmf_object.cstmf_ajax_nonce,
       }
       acfFormSubmit(data,user_id);
      return false;
   })

   // submit code for Information form
   jQuery("#form_id_89").submit(function(e){
       e.preventDefault;
       var data=[];
       var title=jQuery("#acf-field_5d70ae25a90df").val();
       var max_people=jQuery("#acf-field_5d70b50d59d7e").val();
       var minimum_age=jQuery("#acf-field_5d70b51a59d7f").val();
       var information_details=jQuery("#acf-field_5d70b52659d81").val();
       var user_id=jQuery("#_acf_post_id").val();
      
       var data={
          title:title,
          max_people:max_people,
          minimum_age:minimum_age,
          information_details:information_details,
          user_id:user_id,
          action:  'havefan_save_acf_usermeta',
          wp_nonce:cstmf_object.cstmf_ajax_nonce,
       }
       acfFormSubmit(data,user_id);
       return false;
   })

   // submit code for Where we  meet form
   jQuery("#form_id_230").submit(function(e){
       e.preventDefault;
       var data=[];
       var city_where_to_meet=jQuery("#acf-field_5d75e87f19f52").val();
       var address_where_to_meet=jQuery("#acf-field_5d75e88f19f53").val();
       var details_whare_we_meet=jQuery("#acf-field_5d75e91a19f55").val();
       var user_id=jQuery("#_acf_post_id").val();
      
       var data={
          "city-where-to-meet":city_where_to_meet,
          "address-where-to-meet":address_where_to_meet,
          "details-whare-we-meet":details_whare_we_meet,
          user_id:user_id,
          action:  'havefan_save_acf_usermeta',
          wp_nonce:cstmf_object.cstmf_ajax_nonce,
       }
       acfFormSubmit(data,user_id);
       return false;
   })

   // submit code for Guest Requests form 
   jQuery("#form_id_141").submit(function(e){
       e.preventDefault;
       var data=[];
       var guest_requests=jQuery("#acf-field_5d70ded3e2b7b").val();
       var user_id=jQuery("#_acf_post_id").val();
       var data={
          guest_requests:guest_requests,
          user_id:user_id,
          action:  'havefan_save_acf_usermeta',
          wp_nonce:cstmf_object.cstmf_ajax_nonce,
       }
       acfFormSubmit(data,user_id);
      return false;
   })

   // submit code for Included Services form 
   jQuery("#form_id_338").submit(function(e){
       e.preventDefault;
       var data=[];
       var extra_food_services=jQuery("#acf-field_5d78ea98d9919").val();
       var extra_drink_services=jQuery("#acf-field_5d78eace2e1a9").val();
       var extra_ticket_services=jQuery("#acf-field_5d78eae72e1aa").val();
       var extra_transport_services=jQuery("#acf-field_5d78eb227821e").val();
       var extra_tool_services=jQuery("#acf-field_5d78eb217821d").val();
       var extra_other_services=jQuery("#acf-field_5d78eb207821c").val();
       var include_services_base_price=jQuery("#acf-field_5d7cb0d99e0dd").val();
       var user_id=jQuery("#_acf_post_id").val();
       var data={
          extra_food_services:extra_food_services,
          extra_drink_services:extra_drink_services,
          extra_ticket_services:extra_ticket_services,
          extra_transport_services:extra_transport_services,
          extra_tool_services:extra_tool_services,
          extra_other_services:extra_other_services,
          include_services_base_price:include_services_base_price,
          user_id:user_id,
          action:  'havefan_save_acf_usermeta',
          wp_nonce:cstmf_object.cstmf_ajax_nonce,
       }
       acfFormSubmit(data,user_id);
      return false;
   })

   // code for um about form submit using ajax
   jQuery(".cs-profile-form").submit(function(e){
      e.preventDefault;
       var data=[];
       var season_ticket_holder=jQuery("#season-ticket-holder").val();
       var extra_drink_services=jQuery("#user-citys").val();
       var extra_ticket_services=jQuery("#stadium-position").val();
       var languages=jQuery("#languages").val();
       var country=jQuery("#country").val();
       var extra_other_services=jQuery("#team-names").val();
       var user_id=jQuery("#user_id").val();
       var beingafan=jQuery("textarea#being-a-fan").val();
       var biography=jQuery("textarea#biography").val();

       var passion1=jQuery("#passion-1-45").val();
       var passion2=jQuery("#passion-2-45").val();
       var passion3=jQuery("#passion-3-45").val();
       var passion4=jQuery("#passion-4-45").val();
       var passion5=jQuery("#passion-5-45").val();
       var passion6=jQuery("#passion-6-45").val();
       var passion7=jQuery("#passion-7-45").val();
       var passion8=jQuery("#passion-8-45").val();
       var passion9=jQuery("#passion-9-45").val();
       var passion10=jQuery("#passion-10-45").val();

       var data={
          "season-ticket-holder":season_ticket_holder,
          "user-citys":extra_drink_services,
          "stadium-position":extra_ticket_services,
          "languages":languages,
          "country":country,
          "team-names":extra_other_services,
          "being-a-fan":beingafan,
          biography:biography,
          "passion-1":passion1,
          "passion-2":passion2,
          "passion-3":passion3,
          "passion-4":passion4,
          "passion-5":passion5,
          "passion-6":passion6,
          "passion-7":passion7,
          "passion-8":passion8,
          "passion-9":passion9,
          "passion-10":passion10,
          

          user_id:'user_'+user_id,
          action:  'havefan_save_acf_usermeta',
          wp_nonce:cstmf_object.cstmf_ajax_nonce,
       }
       
       acfFormSubmit(data,user_id);
       //jQuery(".cs-profile-form .um-button").removeAttr('disabled');
       jQuery("form.cs-profile-form input[type=submit]").removeAttr('disabled');
      return false;
    })

   // submit code Exp. banner image form 
   // jQuery("#form_id_428").submit(function(e){
   //     e.preventDefault;
   //     var data=[];
   //     var banner_image=jQuery("input[name='acf[field_5d79c2d4c02e8]']").val();
   //     var user_id=jQuery("#_acf_post_id").val();
   //       var data={
   //          banner_image:banner_image,
   //          user_id:user_id,
   //          action:  'havefan_save_acf_usermeta',
   //          wp_nonce:cstmf_object.cstmf_ajax_nonce,
   //       }
   //     acfFormSubmit(data,user_id);
   //     return false;
   // })
   // submit code Exp. banner image form 
   jQuery("#form_id_911").submit(function(e){
       e.preventDefault;
       var arrayField=[];
       jQuery("input[name='acf[field_5d8afd151d6cb][]']").each(function(){
         console.log('abcd',this.value);
         arrayField.push(this.value);
       });
       var data=[];
       var banner_image=jQuery("input[name='acf[field_5d8afd151d6cb][]']").val();
       var user_id=jQuery("#_acf_post_id").val();
         var data={
            experience_banner:arrayField,
            user_id:user_id,
            action:  'havefan_save_acf_usermeta',
            wp_nonce:cstmf_object.cstmf_ajax_nonce,
         }
       acfFormSubmit(data,user_id);
       return false;
   })
   
   

   // submit code for my parsnam information form 
   jQuery("#form_id_284").submit(function(e){
       e.preventDefault;
       var data=[];
       var full_name=jQuery("#acf-field_5d763b0283b04").val();
       var date_of_birth_month=jQuery("#dp1569219021015").val();
       var street_address=jQuery("#acf-field_5d763b8c83b06").val();
       var city=jQuery("#acf-field_5d763b9bee971").val();
       var country=jQuery("#acf-field_5d763ba7ee972").val();
       var postal_address=jQuery("#acf-field_5d763c07cde67").val();
       var user_id=jQuery("#_acf_post_id").val();
       var data={
          full_name:full_name,
          date_of_birth_month:date_of_birth_month,
          street_address:street_address,
          city:city,
          country:country,
          postal_address:postal_address,
          include_services_base_price:include_services_base_price,
          user_id:user_id,
          action:  'havefan_save_acf_usermeta',
          wp_nonce:cstmf_object.cstmf_ajax_nonce,
       }
       acfFormSubmit(data,user_id);
      return false;
   })




   // final code  for acf form submit.


  function acfFormSubmit(data,user_id){
    //alert();
      add_loader();
      var formType=0;
      if(data && data.become_message){
        formType=1;
      }
      jQuery.ajax({
        
        type:    "POST",
        dataType : "json",
        url:    cstmf_object.ajaxurl,
        data:    data,
        success: function(data) {
        // remove_loader(); 
        if(data.status == 'success') {
         // alert(data.message);
         if(formType==1){
          launch_toast('Success',"Your Request send successfully.");
         }else{
          launch_toast('Success',data.message);
         }
         
         // window.location.replace(data.redirect_url);
          return;
        }else{
          alert('fail');
        }
        
        },
        error: function( error ){
        //  alert(data.message);
          launch_toast('Error',data.message)
          remove_loader(); 
        },
      });
    return false;
  }
   
  // notification code 
  function launch_toast(type,msg) {
    var htmlToast='<div id="toast"><div id="img">'+type+'!</div><div id="desc">'+msg+'</div></div>';
     //jQuery("#img").html(type);
     jQuery("body").append(htmlToast);
     var x = document.getElementById("toast")
     x.className = "show";
     setTimeout(function(){ x.className = x.className.replace("show", ""); }, 5000);
  }

    // open Become host form
    jQuery("#becom_host_btn").on('click', function(e){
        e.preventDefault;
        // click("Cling")
        var modal = document.getElementById("BecomeHostModal");
        modal.style.display = "block";
    })
    jQuery('#BecomeHostModal .close').on('click', function(){
        var modal = document.getElementById("BecomeHostModal");
        modal.style.display = "none";
    });

     // submit code for Included Services form 
   jQuery("#become-host-form").submit(function(e){
       e.preventDefault;
       var data=[];
       var become_fname=jQuery("#become_fname").val();
       var become_lname=jQuery("#become_lname").val();
       var become_contact=jQuery("#become_contact").val();
       var become_biography=jQuery("#become-biography").val();
       var become_message=jQuery("#become-message").val();
       var become_stadium=jQuery("#become_stadium").val();
       var become_team=jQuery("#become_team").val();
         var become_country=jQuery("#become_country").val();
         var become_city=jQuery("#become_city").val();
       var user_id=jQuery("#become_user_id").val();
       var data={
          'first_name':become_fname,
          'last_name':become_lname,
          'mobile':become_contact,
          'biography':become_biography,
          'become_message':become_message,
          'stadium-position':become_stadium,
          'team-names':become_team,
          'country': become_country,
          'city': become_city,
          'send-request': 'Yes',
          user_id:user_id,
          action:  'havefan_save_acf_usermeta',
          wp_nonce:cstmf_object.cstmf_ajax_nonce,
       }
       acfFormSubmit(data,user_id);
       jQuery("#BecomeHostModal .close").trigger('click');
      return false;
   })

    // select become country from become host form then showcity
    jQuery('#become_country').on('change', function(){
        var becomeCountry = jQuery(this).val();
        console.log("becomeCountry", becomeCountry);
        var become_cities = getCities(becomeCountry) 
         jQuery("#become_city").select2("val", "");
          var $become_city = jQuery('#become_city');                        
          $become_city.find('option').remove(); 

          $become_city.append('<option disabled="" selected="">Select City</option>');
          jQuery.each(become_cities,function(key, value) 
          {
              $become_city.append('<option value=' + value + '>' + value + '</option>');
          })     
         
    })



    // code for show about section by role
    if(jQuery("#page-user-role").val()=='customer'){ jQuery(".about-being-a-fan , .about-biography , .about-passion, #passion-list").remove() }
    
})


// evant list and calander View using ajax

   function listOutEvant(type,urlGetParameter){
      jQuery("#list_view_selector").val(type);
      jQuery(".event-btn-list-calendar").removeClass('active');
      if(type=='calendar'){
         jQuery(".calendar-btn").addClass('active');
      }else{
         jQuery(".list-btn").addClass('active');
      }
      var data={
           action:  'havefan_event_list',
           wp_nonce:cstmf_object.cstmf_ajax_nonce,
           type:type
       }
      add_loader2();
      jQuery.ajax({
        type:    "POST",
        url:    cstmf_object.ajaxurl+urlGetParameter,
        data:    data,
        success: function(data) {
         remove_loader2(); 
         jQuery('#event-view').html(data);
        
        },
        error: function( error ){
        remove_loader2(); 
        },
      });
    return false;
   }

   function add_loader2(){
      var lodarImage = cstmf_object.site_url+'/assets/image/live_loader.gif';
      jQuery('body').append('<div id="hf_inifiniteLoader" class="lodr-modal"><center></center><span><img src="'+lodarImage+'" style="width: 70px;" /></span></div>');
      jQuery("body #hf_inifiniteLoader").show('fast');
   }
   function remove_loader2(){
      jQuery("body #hf_inifiniteLoader").hide('1000');
      jQuery("body #hf_inifiniteLoader").remove();
   }

  jQuery("form select#by_team").on('change', function(){
      console.log("ff1 team");
      var countryName = jQuery("select#by_country").val();
      var cityName = jQuery("select#by_city").val();
      var teamName = jQuery(this).val();
      var serach_type = 'team';
      SetSearchFromTeamData1( countryName, cityName, teamName, serach_type)
      
   });
jQuery("form select#by_country").on('change', function(){
      console.log("ff country");
       jQuery("input#by_city").val('');
      var countryName = jQuery(this).val();
      var cityName = '';
      var teamName = '';
       var serach_type = 'country';

      // if( countryName == '' || countryName == 'all'){

      //     var default_all_teams=jQuery("#default_all_teams_data").val();
      //     var default_all_teams_data=JSON.parse(default_all_teams) ;
      //     var $select_team = jQuery('#by_team');                        
      //     $select_team.find('option').remove();  
      //     $select_team.append('<option value="">All</option>');
      //     jQuery.each(default_all_teams_data,function(key, value) 
      //     {
      //         $select_team.append('<option value=' + value + '>' + value + '</option>');
      //     })
      //    setDefaultDatesCalendar();
      // }else{
          SetSearchFromTeamData1( countryName, cityName, teamName, serach_type)
      // }
     
      // var cities = getCities(countryName)      
      // console.log({
      //   cities
      // })
   });
jQuery("form select#by_city").on('change', function(){
      console.log("ff city");
      var countryName = jQuery("select#by_country").val();
      var cityName = jQuery(this).val();
      var teamName = '';
       var serach_type = 'city';
      SetSearchFromTeamData1( countryName, cityName, teamName, serach_type)
      
   });
// jQuery("form select#by_city").on('change', function(){
//       console.log("ff");
//       var countryName = jQuery("select#by_country").val();
//       var cityName = jQuery(this).val();
//       var teamName = '';
//        var serach_type = 'city';
//       if( ('' != cityName || cityName == 'all') && (countryName == '' || countryName == 'all')  ){
//           console.log("New Selct city", cityName, "Selct country", countryName)
//            var default_all_teams=jQuery("#default_all_teams_data").val();
//           var default_all_teams_data=JSON.parse(default_all_teams) ;
//           var $select_team = jQuery('#by_team');                        
//           $select_team.find('option').remove();  
//           $select_team.append('<option value="">All</option>');
//           jQuery.each(default_all_teams_data,function(key, value) 
//           {
//               $select_team.append('<option value=' + value + '>' + value + '</option>');
//           })
//          setDefaultDatesCalendar();
//       }else{
//           console.log("Selct city", cityName, "Selct country", countryName)
//           SetSearchFromTeamData1( countryName, cityName, teamName, serach_type)
//       }
      
      
//    });
function SetSearchFromTeamData1( countryName, cityName, teamName, serach_type){
    
      
       var formData = [];
       var formData={
            searchtype:serach_type,
            search_country:countryName,
            search_city:cityName,
            search_team:teamName,
            action:'havefan_set_search_from_field_data',
            //wp_nonce:cstmf_object.cstmf_ajax_nonce,
         } 
         // add_loader();
        jQuery.ajax({
          
          type:    "POST",
          dataType : "json",
          url:    cstmf_object.ajaxurl,
          data:    formData,
          success: function(data) {
            console.log(data);
          // remove_loader(); 
            if(data.status == 'success') {

              if('country' == serach_type ){
                var cities = data.cities;
                   
                    
                  jQuery("#by_city").select2("destroy");
                  jQuery("#by_city").select2();
                  var $select_city = jQuery('#by_city');                        
                  $select_city.find('option').remove();  
                  $select_city.append('<option value="" selected="">All</option>');
                  jQuery.each(cities,function(key, value) 
                  {
                      $select_city.append('<option value="' + value + '">' + value + '</option>');
                     
                  })

                  jQuery("#by_team").select2("destroy");
                  jQuery("#by_team").select2();
                  var teams = data.teams;
                  var $select_team = jQuery('#by_team');   


                  $select_team.find('option').remove();

                  $select_team.append('<option value="" selected="">All</option>');
                  
                  jQuery.each(teams,function(key, value) 
                  {
                      $select_team.append('<option value="' + value + '">' + value + '</option>');
                     
                  })
                  jQuery('span#select2-by_city-container').text('All');
                  jQuery('span#select2-by_city-container').attr('title','All');
                   jQuery('span#select2-by_team-container').text('All');
                  jQuery('span#select2-by_team-container').attr('title','All');
                  
                      // jQuery("#by_city").select2("val", "");
                      // jQuery("#by_team").select2("val", "");
              }else if('city' == serach_type ){
                var teams = data.teams;
                   
                  var $select_team = jQuery('#by_team');                        
                  $select_team.find('option').remove(); 

                  $select_team.append('<option value="" selected="">All</option>');
                  jQuery.each(teams,function(key, value) 
                  {
                     
                      $select_team.append('<option value="' + value + '">' + value + '</option>');
                      
                  })
                  jQuery('span#select2-by_team-container').text('All');
                  jQuery('span#select2-by_team-container').attr('title','All');
                   // jQuery("#by_team").select2("val", "");
              }

 
                 
                jQuery( "#by_date" ).datepicker("destroy" );
                 function available(date) {
                    dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
                    if (jQuery.inArray(dmy, availableDates) != -1) {
                      return [true, "","Available"];
                    } else {
                      return [false,"","unAvailable"];
                    }
                  }
                 
               
                var availableDates = [];
                var all_event_dates = data.all_dates;
                jQuery.each(all_event_dates,function(key, value) 
                {
                  
                  availableDates.push(value);
                    
                })
             
                 jQuery( "#by_date" ).datepicker({ beforeShowDay: available, dateFormat: 'yy-mm-dd',minDate: 0});
              

            }
          
          },
          error: function( error ){
          //  alert(data.message);
            //launch_toast('Error',data.message)
            //remove_loader(); 
          },
        });
      return false;
   }
   
   // if( jQuery('div').hasClass('form_vertical')){
   //      var url_by_country = jQuery('#url_by_country').val();
   //      var url_by_city = jQuery('#url_by_city').val();
   //      var url_by_team = jQuery('#url_by_team').val();
   //      console.log("url_by_country", url_by_country, "url_by_city", url_by_city, "url_by_team", url_by_team)
   //      if( '' ==  url_by_country  ){
   //        console.log("default country")
   //          var countryName = ''
   //          var cityName = '';
   //          var teamName = '';
   //          var serach_type = 'country';
   //          SetSearchFromTeamData1( countryName, cityName, teamName, serach_type)
   //      }else if( '' == url_by_city ){
   //          console.log("default city")
   //          var countryName = url_by_country;
   //          var cityName = '';
   //          var teamName = '';
   //          var serach_type = 'city';
   //          SetSearchFromTeamData1( countryName, cityName, teamName, serach_type)
   //      }else if( '' == url_by_team){
   //          console.log("default team")
   //          var countryName = url_by_country;
   //          var cityName = url_by_city;
   //          var teamName = url_by_team;
   //          var serach_type = 'team';
   //          SetSearchFromTeamData1( countryName, cityName, teamName, serach_type)
   //      }
      

   // }
// var  country   =jQuery("#country").val();
// if(country && country.length>0){
//   console.log("length",country.length);
//   jQuery("#country").prop('disabled', 'disabled');
// }else{
//   console.log("length2",country.length);
// }
// var  city=jQuery("#user-citys").val();
// if( city && city.length>0   ){
//  jQuery("#user-citys ").prop('disabled', 'disabled');
// }
jQuery('ul.search_city_list li span').live('click', function(){
      var cityName = jQuery(this).attr('data-city');
      jQuery('#by_city').val(cityName);
      jQuery('ul.search_city_list').css('display', 'none');
      // console.log("ff");
      var countryName = jQuery("select#by_country").val();
       
      var teamName = '';
      var serach_type = 'city';
      SetSearchFromTeamData1( countryName, cityName, teamName, serach_type)
    console.log("Click");

})
 // jQuery('#by_city').on('change', function() {
 //     var searchKeyword = jQuery(this).val().toLowerCase();
 //      jQuery("ul.search_city_list").empty();
 //       jQuery('ul.search_city_list').append('<li><span data-city="All">All</span></li>');
 //      jQuery('ul.search_city_list').css('display', 'block');
 //     if (searchKeyword.length >= 3) {
 //          // console.log("searchKeyword", searchKeyword);
 //          var countryName = jQuery("#by_country").val()
         
 //          if(countryName && countryName !== ''){
 //              var cityArr = GLOBAL_COUNTRY_CITY_OBJECT.filter(el => el.toLowerCase().startsWith(searchKeyword))
              
 //              jQuery.each(cityArr, function(key, value) {
              
 //                  jQuery('ul.search_city_list').append('<li><span data-city="' + value + '">' + value + '</span></li>');
 //              });
 //          }else{
 //            var cities = Object.values(GLOBAL_CITY_OBJECT)
 //            .reduce((a, b)=> [...a, ...b], [])

 //            var filteredCity = cities.filter(el => el.toLowerCase().startsWith(searchKeyword))
 //            console.log({
 //              filteredCity
 //            })
            
 //              jQuery.each(filteredCity, function(key, value) {
 //                console.log(key, value);
 //                  jQuery('ul.search_city_list').append('<li><span data-city="' + value + '">' + value + '</span></li>');
 //              });
 //          }
         
 //     }
 // });


   jQuery("#country").change(function(){
      console.log("ff",this.value);
      var ctry=this.value;
      jQuery(".city-List").remove();
      jQuery.ajax({
          
          type:    "POST",
          dataType : "json",
          url:   cstmf_object.site_url+'/assets/js/city.json',
          success: function(data) {
            var cty=data[ctry];
            for(var i=0; i<=cty.length ; i++){
              jQuery("#user-citys").append("<option class='city-List'>"+cty[i]+"</option>");
            }

          },
          error: function( error ){
             console.log(error);
          },
        });
    });

function setDefaultDatesCalendar(){
    // set default all evant date.
    if(jQuery('div.toggle-header p').hasClass('toggle-sidebar-event')){
        var defaultEvantDate=jQuery("#available_date_array").val();
    }else{
        var defaultEvantDate=jQuery("#dateJson").val();
    }
    
     var array=JSON.parse(defaultEvantDate) ;//["10-10-2019", "25-10-2019", "8-11-2019"];
     if(array) {
      var data = array;
                   jQuery( "#by_date" ).datepicker("destroy" );
                     function available(date) {
                        dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
                        if (jQuery.inArray(dmy, availableDates) != -1) {
                          return [true, "","Available"];
                        } else {
                          return [false,"","unAvailable"];
                        }
                      }
                    var availableDates = [];
                    var all_event_dates = data;
                    jQuery.each(all_event_dates,function(key, value) 
                    {
                      console.log('value', value );
                      availableDates.push(value);
                        
                    })
                    console.log("availableDates", availableDates)
                     jQuery( "#by_date" ).datepicker({ beforeShowDay: available, dateFormat: 'yy-mm-dd',minDate: 0});
      }
}
if( jQuery("#dateJson").length > 0 ){
    setDefaultDatesCalendar();
}
if(jQuery('div.toggle-header p').hasClass('toggle-sidebar-event')){
      var country_name = jQuery("#by_country").val();
      console.log("country_name",country_name);
      if( ''!= country_name){

          var available_city_array=jQuery("#available_city_array").val();
          GLOBAL_COUNTRY_CITY_OBJECT = JSON.parse(available_city_array) ;
            console.log("county not null", GLOBAL_COUNTRY_CITY_OBJECT);
      }
}




function getCities (cityName = "", cityObject = GLOBAL_CITY_OBJECT){
  return cityObject[cityName]
}
jQuery(document).ready(function($){
    $.getJSON("https://havefan.blsoftware.net/wp-content/themes/BLS_HaveFan_Theme/assets/js/city.json", function(cityObj){
      GLOBAL_CITY_OBJECT = cityObj
    })
})  