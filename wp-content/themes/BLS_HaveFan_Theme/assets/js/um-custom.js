jQuery(document).ready(function($) {
  
   function add_loader(){
      jQuery('body').append('<div id="hf_inifiniteLoader"><span>Please Wait</span></div>');
      jQuery("body #hf_inifiniteLoader").show('fast');
   }
   function remove_loader(){
      jQuery("body #hf_inifiniteLoader").hide('1000');
      jQuery("body #hf_inifiniteLoader").remove();
   }
   jQuery('#EditHaveFunModal .close').on('click', function(){
    var edit_modal = document.getElementById("EditHaveFunModal");
    edit_modal.style.display = "none";
  });
  jQuery('#HaveFunModal .close').on('click', function(){
    var modal = document.getElementById("HaveFunModal");
    modal.style.display = "none";
  });
   
    function getEventForm(event_id, host_id){
        console.log("form getiing")
        var dataString = 'action=get_event_form&event_id='+event_id+'&host_id='+host_id+'&wp_nonce='+cstmf_object.cstmf_ajax_nonce;
        jQuery.ajax({
            type : "post",
            dataType : 'html',
            url : cstmf_object.ajaxurl,
            data : dataString,
            success: function(res) {
                console.log(res);
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
	 
    console.log("Form Is working");
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
					console.log(data);
					 remove_loader(); 
					if(data.status == 'success') {
						jQuery('#HaveFunModal .close').trigger('click');
						alert(data.message);
						 window.location.replace(data.redirect_url);
					}else{
						alert(data.message);
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
     
    console.log("Form Is Update ")
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
					alert(data.message);
					window.location.replace(data.redirect_url);
					return;
				}
				
			  },
			  error: function( error ){
				  remove_loader(); 
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
        console.log(event_id);
        jQuery('#hv_host_event_id').val(event_id);
        var event_title = jQuery('.event-list-'+event_id+ ' .event-title').attr('data-title');
        console.log("event_title", event_title);
        jQuery('#hv_title').val(event_title);
        var modal = document.getElementById("HaveFunModal");
        modal.style.display = "block";
         console.log("Clieck")
    });
   jQuery("span.edit_event_action_btn").live("click", function($){
      
        jQuery('#HaveFunModal .close').trigger('click');
        event_id = jQuery(this).attr('data-val');
        host_id = jQuery(this).attr('data-id');
        getEventForm( event_id, host_id );
    })
  
})